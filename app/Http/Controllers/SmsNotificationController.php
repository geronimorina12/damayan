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

  public function send(Request $request)
{
    $request->validate([
        'deathReport' => 'nullable|string',
        'scheduleContribution' => 'nullable|string',
        'reminders' => 'nullable|string',
        'fundUpdates' => 'nullable|string',
        'type' => 'required|string|in:deathReport,scheduleContribution,reminders,fundUpdates',
    ]);

    $message = null;

    // Fetch all members with contact numbers
    $members = MemberModel::whereNotNull('contact_no')->get();
    if ($members->isEmpty()) {
        Log::warning("No members found with contact numbers for {$request->type} notification.");
        return redirect()->back()->with('error', 'No members with contact numbers.');
    }

    // Handle message type
    if ($request->type == 'deathReport') {
        // Example: assume $request->dead_person_name comes from form input
        $deadPerson = $request->input('dead_person_name', 'one of our members');

        // Delete contributions for all members
        $membersId = MemberModel::pluck('id')->toArray();
        ContributionModel::whereIn('member_id', $membersId)->delete();

        $message = "We regret to inform you that {$deadPerson} has passed away. " .
                   "Last night will be held on 2025-07-17. " .
                   "Collection for burial assistance starts on 2025-07-09.";

    } elseif ($request->type == 'scheduleContribution') {
        foreach ($members as $member) {
            $customMessage = "Hello {$member->name}, thank you for paying your contribution. " .
                             "This message confirms your payment for the upcoming schedule.";

            // Save notification for each member
            $notification = SmsNotificationSaved::create([
                'message' => $customMessage,
                'type' => $request->type
            ]);

            $this->sendAndLog($customMessage, $member->contact_no, $notification->id);
        }

        return redirect()->back()->with('success', 'Schedule contribution notifications sent.');
    } elseif ($request->type == 'reminders') {
        $message = "Hello, our records show you still have an unpaid balance for the contribution (Damayan). " .
                   "Please settle it at your earliest convenience. Thank you.";
    } elseif ($request->type == 'fundUpdates') {
        $message = "Total money disbursed so far is ₱300.00. Thank you for your continuous support.";
    }

    // If no message set, return error
    if (!$message) {
        return redirect()->back()->with('error', 'Message cannot be empty.');
    }

    // Save generic notification in DB
    $notification = SmsNotificationSaved::create([
        'message' => $message,
        'type' => $request->type
    ]);

    // Send SMS and log results
    foreach ($members as $member) {
        $this->sendAndLog($message, $member->contact_no, $notification->id);
    }

    return redirect()->back()->with('success', 'Notification saved and SMS sent to all members.');
}

/**
 * Helper method to send SMS and log success/failure
 */
private function sendAndLog(string $message, string $number, int $notificationId): void
{
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

}
