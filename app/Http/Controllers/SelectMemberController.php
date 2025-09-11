<?php

namespace App\Http\Controllers;

use App\Models\memberModel;
use App\Models\ContributionModel;
use App\Models\SmsNotificationSaved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SmsNotificationSender;

class SelectMemberController extends Controller
{
    /**
     * Death Report
     */
    public function addDeathReport(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'message' => 'required|string'
        ]);

        $members = memberModel::whereIn('id', $request->member_ids)
            ->whereNotNull('contact_number')
            ->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'No valid members with contact numbers.');
        }

        // Delete contributions only for selected members
        ContributionModel::whereIn('member_id', $request->member_ids)->delete();

        $notification = SmsNotificationSaved::create([
            'message' => $request->message,
            'type' => 'deathReport',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($request->message, $member->contact_number, $notification->id);
        }

        return back()->with('success', 'Death report sent to selected members.');
    }

    /**
     * Schedule Contribution
     */
    public function sendScheduleContribution(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'message' => 'required|string'
        ]);

        $members = memberModel::whereIn('id', $request->member_ids)
            ->whereNotNull('contact_number')
            ->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'No valid members with contact numbers.');
        }

        $notification = SmsNotificationSaved::create([
            'message' => $request->message,
            'type' => 'scheduleContribution',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($request->message, $member->contact_number, $notification->id);
        }

        return back()->with('success', 'Schedule contribution sent to selected members.');
    }

    /**
     * Reminders
     */
    public function sendReminders(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'message' => 'required|string'
        ]);

        $members = memberModel::whereIn('id', $request->member_ids)
            ->whereNotNull('contact_number')
            ->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'No valid members with contact numbers.');
        }

        $notification = SmsNotificationSaved::create([
            'message' => $request->message,
            'type' => 'reminders',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($request->message, $member->contact_number, $notification->id);
        }

        return back()->with('success', 'Reminders sent to selected members.');
    }

    /**
     * Fund Updates
     */
    public function sendFundUpdates(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'message' => 'required|string'
        ]);

        $members = memberModel::whereIn('id', $request->member_ids)
            ->whereNotNull('contact_number')
            ->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'No valid members with contact numbers.');
        }

        $notification = SmsNotificationSaved::create([
            'message' => $request->message,
            'type' => 'fundUpdates',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($request->message, $member->contact_number, $notification->id);
        }

        return back()->with('success', 'Fund updates sent to selected members.');
    }

    /**
     * Helper for sending and logging
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
