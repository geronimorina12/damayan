<?php

namespace App\Http\Controllers;

use App\Models\ArchiveContributions;
use App\Models\AssistanceDistribution;
use App\Models\BeneficiaryModel;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use App\Models\User;
use App\Services\SmsNotificationSender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SmsNotificationController extends Controller
{
    public function index()
    {
        $deathReport = SmsNotificationSaved::where('type', 'deathReport')->latest()->first();
        $disbursed = ContributionModel::sum('amount');
        $members = memberModel::select('id', 'first_name', 'last_name')->get()->toArray();

        return Inertia::render('admin/smsNotification/Index', [
            'deathReport' => $deathReport,
            'disbursed' => $disbursed,
            'members' => $members,
        ]);
    }

   public function smsPage()
{
    $deathReport = SmsNotificationSaved::where('type', 'deathReport')->latest()->first();
    $scheduleContribution = SmsNotificationSaved::where('type', 'scheduleContribution')->latest()->first();
    $reminders = SmsNotificationSaved::where('type', 'reminders')->latest()->first();

    $currentFund = AssistanceDistribution::sum('total_amount');

    // Latest assistance distribution (may be null)
    $distributions = AssistanceDistribution::latest()->first();

    $deceasedName = null;

    // ---------------------------------------------
    // 1. If we HAVE a distribution → get that member
    // ---------------------------------------------
    if ($distributions) {
        $deceased = DeathReportModel::where('member_id', $distributions->report_id)
            ->with(['member:id,first_name,last_name'])
            ->first();
        $currentFund = 0;
        $currentFund = AssistanceDistribution::where('report_id', $deceased->deceased_id)
            ->sum('total_amount');
        if ($deceased && $deceased->member) {
            $deceasedName = $deceased->member->first_name . " " . $deceased->member->last_name;
        }
    }

    // ---------------------------------------------------------
    // 2. If NO distribution → use the LATEST DeathReport record
    // ---------------------------------------------------------
    if (!$deceasedName) {
        $latestDeath = DeathReportModel::with(['member:id,first_name,last_name'])
            ->latest() // Get the most recent death report
            ->first();

        Log::info("Latest Death Report fetched: " . ($latestDeath ? json_encode($latestDeath) : 'None'));

        if ($latestDeath) {
            if ($latestDeath->member) {
                $deceasedName = $latestDeath->member->first_name . " " . $latestDeath->member->last_name;
            } else {
                // fallback to the deceased_name in the death report
                $deceasedName = $latestDeath->deceased_name;
            }
        }
    }

    // Final fallback if no deceased name is found at all
    if (!$deceasedName) {
        $deceasedName = "the deceased member";
        Log::warning("No deceased name found in DeathReportModel");
    }

    // Final SMS text
    $fundUpdates = "Total money disbursed so far for {$deceasedName} is {$currentFund}. Thank you for your continuous support.";

    return Inertia::render('admin/SmsPage', [
        'deathReport' => $deathReport,
        'scheduleContribution' => $scheduleContribution,
        'reminders' => "Hello! You still have an unpaid Damayan contribution. Please settle as soon as possible. If already paid, please ignore this message. Thank you",
        'fundUpdates' => $fundUpdates,
        'members' => memberModel::select('id', 'first_name', 'last_name')->get()->toArray(),
        'fundAmount' => $currentFund,
    ]);
}
    public function getMembers(){
        $members = memberModel::select('id', 'first_name', 'last_name')->get()->toArray();
        return response()->json(['members' => $members]);
    }
    public function getDeceased($id){
        $deceased = memberModel::where('id', $id)->first();
        if(!$deceased){
            return response()->json(['message' => 'Member not found'], 404);
        }
        return response()->json(['deceased' => $deceased]);
    }

        public function sendToAllSelected($type, $message, $deceased, $last)
        {
            $members = memberModel::select('id', 'first_name', 'last_name', 'age')->get();
            $users = User::select(['id', 'name'])
            
            ->get();

            $all = collect($members)->merge($users)->sortBy('name')->values()->all();
            return Inertia::render('admin/smsNotification/SendToAllSelected', [
                'members' => $all,
                'type' => $type,
                'message' => $message,
                'deceased' => $deceased,
                'last' => $last,
            ]);
        }

        public function sendToAllSelectedV2($type, $message, $deceased)
        {
            $members = memberModel::select('id', 'first_name', 'last_name', 'age')->get();
            $users = User::select(['id', 'name'])
            ->get();

            $all = collect($members)->merge($users)->sortBy('name')->values()->all();
            return Inertia::render('admin/smsNotification/sendToAllSelectedForDeceased', [
                'members' => $all,
                'type' => $type,
                'message' => $message,
                'deceased' => $deceased
            ]);
        }

    /**
     * Death Report
     */
    public function addDeathReport(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $members = memberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        // delete contributions for all members
        // $memberIds = memberModel::pluck('id')->toArray();
        // ContributionModel::whereIn('member_id', $memberIds)->delete();

        $message = $request->input('message');

        $beneficiaries = memberModel::whereHas('beneficiaries')->with('beneficiaries')->get();
        $notification = SmsNotificationSaved::create([
            'message' => $message,
            'type' => 'deathReport',
        ]);
        

        foreach ($members as $member) {
            $this->sendAndLog($message, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Death report saved and SMS sent to all members.');
    }

    /**
     * Schedule Contribution
     */
        public function sendScheduleContribution(Request $request)
{
    try {
        $request->validate([
            'id' => 'required',
            'message' => 'required|string',
            'contact_number' => 'required|string',
            'collector' => 'nullable|string',
            'purok' => 'required|string',
            'deceasedId' => 'nullable'
        ]);

        $members = memberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        $message = $request->input('message');

        $notification = SmsNotificationSaved::create([
            'message' => $message,
            'type' => 'scheduleContribution',
        ]);

        // Convert to normalized form
        $normalizedPurok = $this->normalizePurok($request->purok);

        // 🔍 CHECK IF CONTRIBUTION ALREADY EXISTS
        $exists = ContributionModel::where('member_id', $request->id)
            ->where('deceased_id', $request->deceasedId)
            ->exists();

        if (!$exists) {
            // ✔ Create new contribution only if not existing
            ContributionModel::create([
                'member_id' => $request->id,
                'amount' => 100,
                'payment_date' => now(),
                'updated_by' => Auth::id(),
                'collector' => $request->collector ?: "",
                'purok' => $normalizedPurok,
                'status' => "paid",
                'deceased_id' => $request->deceasedId,
            ]);

            ArchiveContributions::create([
                'member_id' => $request->id,
                'amount' => 100,
                'payment_date' => now(),
                'updated_by' => Auth::id(),
                'collector' => $request->collector ?: "",
                'purok' => $normalizedPurok,
                'status' => "paid",
                'deceased_id' => $request->deceasedId,
            ]);
        }

        // Send SMS after processing
        $this->sendAndLog($request->message, $request->contact_number, $notification->id);

        return redirect()->back()->with('success', 'Schedule contribution notifications sent.');
    } catch (\Exception $e) {
        Log::error('Error in sendScheduleContribution: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while sending schedule contribution: ' . $e->getMessage());
    }
}


        /**
         *  Helper function to normalize Purok format
         * Converts "Purok 1" → "purok1"
         */
        private function normalizePurok($purok)
        {
            if (!$purok) {
                return '';
            }

            // Remove spaces, lowercase the string
            return strtolower(str_replace(' ', '', $purok));
        }


    /**
     * Reminders
     */
    public function sendReminders(Request $request)
    {
         $validated = $request->validate([
        'message' => 'required|string',
        'type' => 'required|string',
        'memberId' => 'required|integer',
        'lastNight' => 'required|date',
        'startOfContribution' => 'required|date',
         ]);
        $members = memberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        $message = $request->input('message');

         $deceased = memberModel::find($validated['memberId']);
        if (!$deceased) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.',
            ], 404);
        }

        $notification = SmsNotificationSaved::create([
            'message' => $validated['message'],
            'type' => 'reminders',
        ]);
              DeathReportModel::create([
                'reported_by' => Auth::id(),
                'member_id' => $validated['memberId'],
                'deceased_name' => $deceased->first_name . ' ' . $deceased->last_name,
                'date_of_death' => $validated['startOfContribution'],
                'report_date' => now(),
                'last_night' => $validated['lastNight'],
            ]);
            $deceased->delete();

        foreach ($members as $member) {
            $this->sendAndLog($message, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Reminders saved and SMS sent to all members.');
    }

    /**
     * Fund Updates
     */
    public function sendFundUpdates(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $members = memberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        $message = $request->input('message');

        $notification = SmsNotificationSaved::create([
            'message' => $message,
            'type' => 'fundUpdates',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($message, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Fund updates saved and SMS sent to all members.');
    }

            /**
             * Helper for sending and logging
             */
            /**
         * Sends an SMS message and logs the result.
         *
         * @param string $message The SMS message content.
         * @param string $number The recipient phone number.
         * @param int $notificationId The related notification record ID.
         */
        private function sendAndLog(string $message, string $number, int $notificationId): void
        {
            // Log the outgoing message (TEST MODE)
            Log::info("FAKE SMS (TEST MODE): Sending to {$number} | Notification ID: {$notificationId} | Message: {$message}");

            try {
                // Attempt to send SMS using a service class (Semaphore, Twilio, etc.)
                $success = SmsNotificationSender::send($message, [$number]);

                // Evaluate the send result
                if ($success) {
                    Log::info("SMS successfully sent to {$number} | Notification ID: {$notificationId}");
                } else {
                    Log::error("SMS failed to send to {$number} | Notification ID: {$notificationId}");
                }

            } catch (\Throwable $e) {
                // Log unexpected errors (network issues, API errors, etc.)
                Log::error(
                    "Error sending SMS to {$number} | Notification ID: {$notificationId} | Exception: {$e->getMessage()}"
                );
            }
        }

    public function selectDeceased()
    {
        $members = memberModel::select('id', 'first_name', 'last_name', 'age')->get()->toArray();

        return Inertia::render('admin/SelectDeceased', [
            'members' => $members
        ]);
    }
}
