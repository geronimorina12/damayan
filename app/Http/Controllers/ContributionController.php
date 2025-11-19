<?php

namespace App\Http\Controllers;

use App\Models\ArchiveContributions;
use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\User;
use App\Services\SmsNotificationSender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContributionController extends Controller
{
    public function index(){
        $mem = memberModel::with('contributions')
        ->orderBy('first_name', 'asc')
        ->paginate(10);
        $selectedPurok = 'all';
        $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();

          $currentDeceasedMembers = DeathReportModel::where('iscurrent', true)
     ->get();
     $currentDeceasedMember = DeathReportModel::where('iscurrent', true)
     ->latest('created_at')
     ->first();

        return Inertia::render('admin/dashboard/contribution/MemberContribution', [
            'member' => $mem,
            'selectedPurok' => $selectedPurok,
            'collectors' => $collectors,
            'currentDeceasedMembers' => $currentDeceasedMembers,
            'currentDeceasedMember' => $currentDeceasedMember,
        ]);
    }

public function toggleContributionPurok($purok, $deceasedId)
{
    switch ($purok) {
        case 'all':
            $formatPurok = 'all';
            break;
        case 'purok1':
            $formatPurok = 'Purok 1';
            break;
        case 'purok2':
            $formatPurok = 'Purok 2';
            break;
        case 'purok3':
            $formatPurok = 'Purok 3';
            break;
        case 'purok4':
            $formatPurok = 'Purok 4';
            break;
        default:
            $formatPurok = '';
    }

         
     if($deceasedId !== 0){
        $currentDeceasedMember = DeathReportModel::where('member_id', $deceasedId)
     ->latest('created_at')
     ->first();
     }else{
        $currentDeceasedMember = null;
     }
    $mem = memberModel::where('purok', $formatPurok)
        ->with(['contributions' => function($query) use ($deceasedId, $currentDeceasedMember) {
            $query->where('deceased_id', $deceasedId ?: $currentDeceasedMember->member_id ?? 0);
        }])
        ->paginate(10);

    if (count($mem->items()) === 0 && $purok === 'all') {
          $mem = memberModel::with(['contributions' => function($query) use ($deceasedId) {
            $query->where('deceased_id', $deceasedId);
        }])->paginate(10);
    }

    $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();

    $currentDeceasedMembers = DeathReportModel::where('iscurrent', true)
     ->get();

    return Inertia::render('admin/dashboard/contribution/MemberContribution', [
        'member'        => $mem,
        'selectedPurok' => $purok,
        'collectors'    => $collectors,
        'currentDeceasedMembers' => $currentDeceasedMembers,
        'currentDeceasedMember' => $currentDeceasedMember,
    ]);
}


    public function add(){
        $members = memberModel::select('id', 'first_name', 'last_name', 'middle_name', 'purok')
        ->orderBy('first_name', 'asc')
        ->get();
        $users = User::select('id', 'name', 'role')
        ->orderBy('name', 'asc')
        ->get(); 
        $paidMembersId = ContributionModel::pluck('member_id')->toArray();
        $deceasedMembers = DeathReportModel::select('member_id', 'deceased_name')->get();

        return Inertia::render('admin/dashboard/contribution/AddContribution', [
            'members' => $members,
            'users' => $users,
            'paidMembersId' => $paidMembersId,
            'deceasedMembers' => $deceasedMembers,
        ]);
    }

    public function getMembersData(){
         $members = memberModel::select('id', 'first_name', 'last_name', 'middle_name', 'purok')
        ->orderBy('first_name', 'asc')
        ->get();

    $users = User::select('id', 'name', 'role', 'purok')
        ->orderBy('name', 'asc')
        ->get();

    $paidMembersId = ContributionModel::pluck('member_id')->toArray();

    $deceasedMembers = DeathReportModel::select('report_id', 'member_id', 'deceased_name')->get();

    return response()->json([
        'members' => $members,
        'users' => $users,
        'paidMembersId' => $paidMembersId,
        'deceasedMembers' => $deceasedMembers,
    ]);
    }


     public function store(Request $request)
{
    $request->validate([
        'member_id' => 'required|exists:members,id',
        'amount' => 'numeric|min:0',
        'payment_date' => 'required|date',
        'collector' => 'nullable|max:255',
        'purok' => 'required',
        'status' => 'required',
        'deceased_id' => 'nullable|exists:death_reports,member_id',
    ]);
    Log::info("Purok: " . $request->purok ?: "null");
    $member = memberModel::find($request->member_id);
    $message = "Dear {$member->first_name} {$member->last_name}, your contribution of {$request->amount} has been recorded on {$request->payment_date}. Thank you for your support!";   
    $this->sendAndLog($message, $member->contact_number, $notification->id ?? 0);
    ContributionModel::create([
        'member_id' => $request->member_id,
        'amount' => $request->amount,
        'payment_date' => $request->payment_date,
        'updated_by' => Auth::id(),
        'collector' => $request->collector ?: "",
        'purok' => $request->purok,
        'status' => $request->status,
        'deceased_id' => $request->deceased_id,
    ]);

     ArchiveContributions::create([
        'member_id' => $request->member_id,
        'amount' => $request->amount,
        'payment_date' => $request->payment_date,
        'updated_by' => Auth::id(),
        'collector' => $request->collector ?: "",
        'purok' => $request->purok,
        'status' => $request->status,
        'deceased_id' => $request->deceased_id,
    ]);
    return redirect()->back()->with('success', 'Contribution created successfully.');
}
  public function toggle($id, $purok)
{
    // Log::info("Purok: " . $purok ?: "null");
    $cleaned = trim($purok);
    
    // Add space between "purok" and the number using regex
    $formatted = preg_replace('/(purok)(\d+)/i', '$1 $2', $cleaned);
    
    // Capitalize the first letter of each word
    $formatted = ucwords(strtolower($formatted));
   $mem = memberModel::where('purok', $formatted)->with(['contributions' => function ($query) use ($id) {
        $query->where('deceased_id', $id);
    }])
    ->orderBy('first_name', 'asc')
    ->paginate(10);
    $selectedPurok = $purok;

    $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();

         $currentDeceasedMembers = DeathReportModel::where('iscurrent', true)
     ->get();
     $currentDeceasedMember = DeathReportModel::where('member_id', $id)
     ->latest('created_at')
     ->first();

    if(Auth::user()->role === 'admin'){
        return Inertia::render('admin/dashboard/contribution/MemberContribution', [
        'member' => $mem,
        'selectedPurok' => $selectedPurok,
        'collectors' => $collectors,
        'currentDeceasedMembers' => $currentDeceasedMembers,
        'currentDeceasedMember' => $currentDeceasedMember,
    ]);
    }else{
        return Inertia::render('collector/contribution/MemberContribution', [
            'member' => $mem->items(),
            'selectedPurok' => $selectedPurok,
            'collectors' => $collectors,
            'currentDeceasedMembers' => $currentDeceasedMembers,
            'currentDeceasedMember' => $currentDeceasedMember,
        ]);
    }
}

public function sendReminderBeforeLastNight()
{
    $today = Carbon::today();

    // Fetch reports where last_night is exactly 2 days ahead
    $reports = DeathReportModel::whereDate('last_night', $today->copy()->addDays(2))
        ->where('iscurrent', 1)
        ->get();

    if ($reports->isEmpty()) {
        Log::info("No death reports found for reminder sending.");
        return;
    }

    // Fetch all members with a contact number
    $members = MemberModel::whereNotNull('contact_number')
        ->where('contact_number', '!=', '')
        ->get();

    if ($members->isEmpty()) {
        Log::warning("No members with contact number available to send reminders.");
        return;
    }

    foreach ($reports as $report) {

        $formattedDate = Carbon::parse($report->last_night)->format('F d, Y');

        foreach ($members as $member) {

            $message = "Reminder: The Last Night viewing for {$report->deceased_name} "
                     . "will be on {$formattedDate}. Please join us in prayer.";

            $this->sendAndLog($message, $member->contact_number, $report->report_id);

            Log::info("Reminder SMS queued | Member: {$member->id} | Report: {$report->report_id}");
        }
    }
}

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
