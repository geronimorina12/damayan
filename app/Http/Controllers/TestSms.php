<?php

namespace App\Http\Controllers;

use App\Models\AssistanceDistribution;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Services\SmsNotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestSms extends Controller
{
    public function run(){
        $today = now()->startOfDay();

    // Find deceased records where last_night is 2 days from today
    $deceased = DeathReportModel::where('member_id', 161)->first();


    if (!$deceased) {
        Log::info("No deceased record found where last_night is exactly 2 days from today.");
        return;
    }

    Log::info("Found deceased: {$deceased->deceased_name}, last_night = {$deceased->last_night}, today = {$today}");

    // Send SMS notification
    $message = "Hello! You still have an unpaid Damayan contribution. Please settle as soon as possible. If already paid, please ignore this message. Thank you";

    $members = memberModel::whereNotNull('contact_number')->get();

    if ($members->isEmpty()) {
        Log::warning("No members found with contact numbers to send SMS.");
        return;
    }

    foreach ($members as $member) {
        $this->sendAndLog($message, $member->contact_number, 0);
    }

    $this->cleanupDeceasedContributions();
    Log::info("DailyTask completed. Message sent to all members.");
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

    // Make a distributions record based on data in ContributionModel
    public function makeDistribution(){
        $groupedContributions = ContributionModel::select('deceased_id')
        ->selectRaw('SUM(amount) as total_amount')
        ->groupBy('deceased_id')
        ->get();

    foreach ($groupedContributions as $group) {
        AssistanceDistribution::create([
            'report_id' => $group->deceased_id, 
            'total_amount' => $group->total_amount,
            'distribution_date' => now(),
        ]);
    }
    }
    /**
 * Cleanup contributions & deceased records whose last_night == today
 */
private function cleanupDeceasedContributions(): void
{
    Log::info("Starting cleanupDeceasedContributions...");

    $todayStart = now()->startOfDay();
    $todayEnd = now()->endOfDay();

    // Get deceased whose 'last_night' is exactly today
    $toCleanup = DeathReportModel::whereNotNull('last_night')
        ->whereBetween('last_night', [$todayStart, $todayEnd])
        ->get();

    if ($toCleanup->isEmpty()) {
        Log::info("No deceased found for cleanup (last_night = today).");
        return;
    }

    foreach ($toCleanup as $deceased) {
        Log::info("Cleaning records for deceased with ID: {$deceased->id}");

        // 1️ Delete all contributions for this deceased member
        $deletedContributions = ContributionModel::where('member_id', $deceased->member_id)->delete();
        Log::info("Deleted {$deletedContributions} contributions for deceased member_id: {$deceased->member_id}");

        // 2️ Delete the deceased record itself
        $deceased->delete();
        Log::info("Deleted deceased record with ID: {$deceased->id}");
    }

    Log::info("cleanupDeceasedContributions completed.");
}


    
}
