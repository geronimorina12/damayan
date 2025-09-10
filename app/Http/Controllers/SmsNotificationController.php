<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use App\Services\SmsNotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SmsNotificationController extends Controller
{
    public function index(){
        $deathReport = DeathReportModel::latest()->first();
        $disbursed = ContributionModel::sum('amount'); 
        $members = memberModel::select('id', 'first_name', 'last_name')->get()->toArray();
        return Inertia::render('admin/smsNotification/Index', [
            'deathReport' => $deathReport,
            'disbursed' => $disbursed,
            'members' => $members,
        ]);
    }
    public function smsPage(){ //Pages/admin/SmsPage.vue
        
        $deathReport = SmsNotificationSaved::where('type', 'deathReport')->latest()->first();
        $scheduleContribution = SmsNotificationSaved::where('type', 'scheduleContribution')->latest()->first();
        $reminders = SmsNotificationSaved::where('type', 'reminders')->latest()->first();
        $fundUpdates = SmsNotificationSaved::where('type', 'fundUpdates')->latest()->first();

        return Inertia::render('admin/SmsPage', [
            'deathReport' => $deathReport,
            'scheduleContribution' => $scheduleContribution,
            'reminders' => $reminders,
            'fundUpdates' => $fundUpdates
        ]);
    }

   /**
     * Add death report: server builds the message and sends to all members with contact_number.
     */
    public function addDeathReport(Request $request)
    {
        $request->validate([
            'dead_person_name' => 'nullable|string'
        ]);

        $members = MemberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            Log::warning('No members found with contact numbers for death report.');
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        // Example extra logic you previously wanted: delete contributions for all members
        $memberIds = MemberModel::pluck('id')->toArray();
        ContributionModel::whereIn('member_id', $memberIds)->delete();

        $deadPerson = $request->input('dead_person_name', 'one of our members');

        $message = "We regret to inform you that {$deadPerson} has passed away. " .
                   "Last night will be held on 2025-07-17. " .
                   "Collection for burial assistance starts on 2025-07-09.";

        // Save a single notification record
        $notification = SmsNotificationSaved::create([
            'message' => $message,
            'type' => 'deathReport',
        ]);

        // Send to each member's contact_number and log
        foreach ($members as $member) {
            $this->sendAndLog($message, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Death report saved and SMS sent to all members.');
    }

    /**
     * Schedule contribution: builds a per-member message (personalized) and sends.
     */
    public function sendScheduleContribution(Request $request)
    {
        $members = MemberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            Log::warning('No members found with contact numbers for schedule contribution.');
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        foreach ($members as $member) {
            // determine a display name (adapt to your member fields)
            $memberName = $member->name ?? trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? ''));
            $memberName = $memberName ?: 'member';

            $customMessage = "Hello {$memberName}, thank you for paying your contribution. " .
                             "This message confirms your payment for the upcoming schedule.";

            // you previously created a saved notification per member — keeping same behavior:
            $notification = SmsNotificationSaved::create([
                'message' => $customMessage,
                'type' => 'scheduleContribution',
            ]);

            $this->sendAndLog($customMessage, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Schedule contribution notifications sent.');
    }

    /**
     * Reminders: server-built generic message sent to all members.
     */
    public function sendReminders(Request $request)
    {
        $members = MemberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            Log::warning('No members found with contact numbers for reminders.');
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        $message = "Hello, our records show you still have an unpaid balance for the contribution (Damayan). " .
                   "Please settle it at your earliest convenience. Thank you.";

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
     * Fund updates: server-built generic message sent to all members.
     */
    public function sendFundUpdates(Request $request)
    {
        $members = MemberModel::whereNotNull('contact_number')->get();
        if ($members->isEmpty()) {
            Log::warning('No members found with contact numbers for fund updates.');
            return redirect()->back()->with('error', 'No members with contact numbers.');
        }

        // You can update the amount dynamically if you calculate it here.
        $message = "Total money disbursed so far is ₱300.00. Thank you for your continuous support.";

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
     * Helper: perform the actual SMS send and log result.
     */
    private function sendAndLog(string $message, string $number, int $notificationId): void
    {
        try {
            // adjust this call if your sender API/class signature differs
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
        $members = MemberModel::select('id', 'first_name', 'last_name', 'age')->get()->toArray();
        return inertia('admin/SelectDeceased', [
            'members' => $members
    ]);
}


}
