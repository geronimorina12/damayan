<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryModel;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SmsNotificationSavedController extends Controller
{
   
    public function send(Request $request){
         $request->validate([
        'deathReport' => 'nullable|string',
        'scheduleContribution' => 'nullable|string',
        'reminders' => 'nullable|string',
        'fundUpdates' => 'nullable|string',
        'type' => 'required|string',
        'memberId' => 'required',
        'message' => 'required|string'
        ]);

        if($request->type == 'deathReport'){
           // Get all member IDs from members table
            $membersId = memberModel::pluck('id')->toArray(); // pluck gives a flat array directly
            $deceased = memberModel::with('beneficiaries')->findOrFail($request->memberId);

            // create first a record for death person 
            DeathReportModel::create([
                'reported_by' => Auth::id(),
                'member_id' => $request->memberId,
                'deceased_name' => $request->deceasedName ?: $deceased->first_name . $deceased->last_name,
                'date_of_death' => $request->dateOfDeath ?: now(),
                'report_date' => now(),
                'last_night' => $request->lastNight ?: null,
            ]);
            if($deceased->beneficiaries[0]){
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
            $member = memberModel::find($request->memberId);
            if($member){
                $member->delete();
            }

            SmsNotificationSaved::create([
                'message' => $request->deathReport ?: $request->message,
                'type' => $request->type
            ]);
        }else if($request->type == 'scheduleContribution'){
            SmsNotificationSaved::create([
                'message' => $request->scheduleContribution ?: $request->message,
                'type' => $request->type
            ]);
        }else if($request->type == 'reminders'){
            SmsNotificationSaved::create([
                'message' => $request->reminders ?: $request->message,
                'type' => $request->type
            ]);
        }else if($request->type == 'fundUpdates'){
            SmsNotificationSaved::create([
                'message' => $request->fundUpdates ?: $request->message,
                'type' => $request->type
            ]);
        }
        
    return redirect()->back()->with('success', 'Notification data saved.');
    }
}