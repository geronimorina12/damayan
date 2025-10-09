<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ]);

        if($request->type == 'deathReport'){
           // Get all member IDs from members table
            $membersId = memberModel::pluck('id')->toArray(); // pluck gives a flat array directly

            // Don't delete the user even he/she is dead according to prototype
            // ContributionModel::whereIn('member_id', $membersId)->delete();
            // $member = memberModel::find($request->memberId);
            // if($member){
            //     $member->delete();
            // }
            DeathReportModel::create([
                'reported_by' => Auth::id(),
                'member_id' => $request->memberId,
                'deceased_name' => $request->deceasedName,
                'date_of_death' => $request->dateOfDeath,
                'report_date' => now(),
            ]);

            SmsNotificationSaved::create([
                'message' => $request->deathReport,
                'type' => $request->type
            ]);
        }else if($request->type == 'scheduleContribution'){
            SmsNotificationSaved::create([
                'message' => $request->scheduleContribution,
                'type' => $request->type
            ]);
        }else if($request->type == 'reminders'){
            SmsNotificationSaved::create([
                'message' => $request->reminders,
                'type' => $request->type
            ]);
        }else if($request->type == 'fundUpdates'){
            SmsNotificationSaved::create([
                'message' => $request->fundUpdates,
                'type' => $request->type
            ]);
        }
        
    return redirect()->back()->with('success', 'Notification data saved.');
    }
}