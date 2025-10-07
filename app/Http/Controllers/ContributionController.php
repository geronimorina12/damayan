<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\memberModel;
use App\Models\User;
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
        return Inertia::render('admin/dashboard/contribution/MemberContribution', [
            'member' => $mem,
            'selectedPurok' => $selectedPurok,
            'collectors' => $collectors,
        ]);
    }

public function toggleContributionPurok($purok)
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

    $mem = memberModel::where('purok', $formatPurok)
    ->with('contributions')->paginate(10);

    if ($mem->isEmpty() && $purok === 'all') {
        $mem = memberModel::with('contributions')->get();
    }

    $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();

    return Inertia::render('admin/dashboard/contribution/MemberContribution', [
        'member'        => $mem,
        'selectedPurok' => $purok,
        'collectors'    => $collectors,
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
        return Inertia::render('admin/dashboard/contribution/AddContribution', [
            'members' => $members,
            'users' => $users,
            'paidMembersId' => $paidMembersId,
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
    ]);
    ContributionModel::create([
        'member_id' => $request->member_id,
        'amount' => $request->amount,
        'payment_date' => $request->payment_date,
        'updated_by' => Auth::id(),
        'collector' => $request->collector ?: "",
        'purok' => $request->purok,
        'status' => $request->status,
    ]); 

    return redirect()->back()->with('success', 'Contribution created successfully.');
}
}
