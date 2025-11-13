<?php

namespace App\Console\Commands;

use App\Models\AssistanceDistribution;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use App\Services\SmsNotificationSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    Log::info("Running DailyTask scheduler...");

    //  Fetch latest deceased record
    $deceased = DeathReportModel::whereNotNull('last_night')
        ->orderBy('last_night', 'desc')
        ->first();

    if (!$deceased) {
        Log::info("No deceased record found with last_night not null.");
        return;
    }

    //  Check if today is exactly 2 days BEFORE last_night
    $twoDaysBefore = \Carbon\Carbon::parse($deceased->last_night)->subDays(2)->startOfDay();
    $today = now()->startOfDay();

    if (!$twoDaysBefore->equalTo($today)) {
        Log::info("Not yet time to send SMS. last_night - 2 days = {$twoDaysBefore}, today = {$today}");
        return;
    }

    // $message = "Good day. We would like to inform you in advance that an individual is expected to be laid to rest soon, based on our records from two days prior. Let us keep the bereaved family in our thoughts.";
    $message = "Hello! You still have an unpaid Damayan contribution. Please settle as soon as possible. If already paid, please ignore this message. Thank you";

    //  Get members with contact numbers
    $members = memberModel::whereNotNull('contact_number')->get();

    if ($members->isEmpty()) {
        Log::warning("No members found with contact numbers to send SMS.");
        return;
    }

    //  Send SMS to each member
    foreach ($members as $member) {
        $this->sendAndLog($message, $member->contact_number, 0);
    }

    // Run cleanup of deceased whose last_night was yesterday
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
