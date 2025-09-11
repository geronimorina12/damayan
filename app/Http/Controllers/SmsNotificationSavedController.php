<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\SmsNotificationSaved;
use Illuminate\Http\Request;
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

            // Delete contributions where member_id is in $membersId
            ContributionModel::whereIn('member_id', $membersId)->delete();


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