<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryModel;
use App\Models\memberModel;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\NotificationModel;
use App\Models\SmsNotificationSaved;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SmsNotificationSender;
use Illuminate\Support\Facades\Auth;

class SelectMemberController extends Controller
{
    /**
     * Death Report
     */
    public function addDeathReport(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'message' => 'required|string',
            'deceased' => 'nullable',
            'last' => 'string'
        ]);
        $members = memberModel::whereIn('id', $request->member_ids)
            ->whereNotNull('contact_number')
            ->get();

        $deceased = memberModel::with('beneficiaries')->findOrFail($request->deceased);

          DeathReportModel::create([
                'reported_by' => Auth::id(),
                'member_id' => $deceased->id,
                'deceased_name' => $deceased->first_name . ' ' . $deceased->last_name,
                'date_of_death' => now(),
                'report_date' => now(),
                'last_night' => $request->last ?: null,
            ]);

       $collectors = User::where('role', 'collector')->get(['id']);

            if ($collectors->isNotEmpty()) {
                foreach ($collectors as $collector) {
                    NotificationModel::create([
                        'user_id' => $collector->id,
                        'message' => $request->message,
                    ]);
                }
            }
        if ($members->isEmpty()) {
            return back()->with('error', 'No valid members with contact numbers.');
        }

    if($deceased->beneficiaries->isNotEmpty()){
                $nameParts = explode(' ', trim($deceased->beneficiaries[0]->name));
                $firstName = $nameParts[0] ?? 'Unknown';
                $lastName = count($nameParts) > 1 
                    ? implode(' ', array_slice($nameParts, 1)) 
                    : $deceased->last_name; 

                // make the first beneficiary as new member
               $newMember = memberModel::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'address' => $deceased->address ?: 'Bonga, Bulan, Sorsogon',
                    'contact_number' => $deceased->contact_number,
                    'date_of_birth' => $deceased->beneficiaries[0]->birth_date,
                    'registration_date' => now(),
                    'purok' => $deceased->purok,
                    'age' => $deceased->beneficiaries[0]->age,
                    'middle_name' => '',
                    'status' => 'active',
                    'occupation' => 'N/A',
                ]);
                //delete that first beneficiary since cya na ang new member
                BeneficiaryModel::where('user_id', $deceased->beneficiaries[0]->user_id)->delete();

                //update the rest of beneficiaries to new member id
                BeneficiaryModel::where('user_id', $deceased->id)->update(['user_id' => $newMember->id]);
            }

            // delete the deceased member 
            $member = memberModel::find($deceased->id);
            if($member){
                $member->delete();
            }


        foreach ($members as $member) {
            $this->sendAndLog($request->message, $member->contact_number, 0);
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
}
