<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use App\Services\SmsNotificationSender;
use Illuminate\Http\Request;
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
        $fundUpdates = SmsNotificationSaved::where('type', 'fundUpdates')->latest()->first();

        return Inertia::render('admin/SmsPage', [
            'deathReport' => $deathReport,
            'scheduleContribution' => $scheduleContribution,
            'reminders' => $reminders,
            'fundUpdates' => $fundUpdates,
            'members' => memberModel::select('id', 'first_name', 'last_name')->get()->toArray(),
        ]);
    }
        public function sendToAllSelected($type, $message)
        {
            $members = memberModel::select('id', 'first_name', 'last_name', 'age')->get();

            return Inertia::render('admin/smsNotification/SendToAllSelected', [
                'members' => $members,
                'type' => $type,
                'message' => $message,
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
        $memberIds = memberModel::pluck('id')->toArray();
        ContributionModel::whereIn('member_id', $memberIds)->delete();

        $message = $request->input('message');

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
            'type' => 'scheduleContribution',
        ]);

        foreach ($members as $member) {
            $this->sendAndLog($message, $member->contact_number, $notification->id);
        }

        return redirect()->back()->with('success', 'Schedule contribution notifications sent.');
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
        // try {
        //     $success = SmsNotificationSender::send($message, [$number]);

        //     if ($success) {
        //         Log::info("SMS sent successfully to {$number} | Notification ID: {$notificationId}");
        //     } else {
        //         Log::error("SMS failed to send to {$number} | Notification ID: {$notificationId}");
        //     }
        // } catch (\Exception $e) {
        //     Log::error("Exception when sending SMS to {$number}: " . $e->getMessage());
        // }
    }

    public function selectDeceased()
    {
        $members = memberModel::select('id', 'first_name', 'last_name', 'age')->get()->toArray();

        return Inertia::render('admin/SelectDeceased', [
            'members' => $members
        ]);
    }
}
