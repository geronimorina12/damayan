<?php

namespace App\Http\Controllers;

use App\Models\ArchiveContributions;
use App\Models\AssistanceDistribution;
use App\Models\BeneficiaryModel;
use App\Models\ContributionModel;
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

        $fundUpdates = "Total money disbursed so far is " . $currentFund . ". Thank you for your continuous support.";
        return Inertia::render('admin/SmsPage', [
            'deathReport' => $deathReport,
            'scheduleContribution' => $scheduleContribution,
            'reminders' => "Hello! You still have an unpaid Damayan contribution. Please settle as soon as possible. If already paid, please ignore this message. Thank you",
            'fundUpdates' => $fundUpdates,
            'members' => memberModel::select('id', 'first_name', 'last_name')->get()->toArray(),
        ]);
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

                //  Convert "Purok 1" → "purok1"
                $normalizedPurok = $this->normalizePurok($request->purok);

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
            'type' => 'reminders',
        ]);

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
    private function sendAndLog(string $message, string $number, int $notificationId): void
    {
            Log::info("FAKE SMS (TEST MODE) to {$number} | Notification ID: {$notificationId} | Message: {$message}");
         try {
             $success = SmsNotificationSender::send($message, [$number]);

             if ($success) {
                 Log::info("SMS sent successfully to {$number} | Notification ID: {$notificationId}");
             } else {
                 Log::error("SMS failed to send to {$number} | Notification ID: {$notificationId}");
             }
         } catch (\Exception $e) {
             Log::error("Exception when sending SMS to {$number}: " . $e->getMessage());
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
