<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\memberModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ReportController extends Controller
{
     public function index()
    {

    $collectors = User::where('role', 'collector')->select('id', 'name', 'purok')->get();

    // Load all contributions with needed fields
    $contributions = ContributionModel::select('collector', 'status', 'amount', 'created_at')->get();

    $collectorStats = $collectors->map(function ($collector) use ($contributions) {
        // Filter contributions by matching collector name
        $matchedContributions = $contributions->where('collector', $collector->name);
        $assignedMembers = memberModel::whereRaw(
            'RIGHT(purok, 1) = ?',
            [substr($collector->purok, -1)]
        )->get();

    return [
        'collector' => $collector->name,
        'purok' => $collector->purok,
        'members' => $assignedMembers->count(),
        'paid' => $matchedContributions->where('status', 'paid')->count(),
        'not_paid' => $matchedContributions->where('status', 'not_paid')->count(),
        'total_amount' => $matchedContributions->sum('amount'),
        'date' => $matchedContributions->max('created_at'),
    ];
});

        return Inertia::render('admin/dashboard/report/ReportHome', [
            'contributions' => $collectorStats,
        ]);
    }

    public function toggleStatus($status, $purok) // In call lang ang purok para ma return sa frontend
    {
        // same logic san index method, pero dd in filter ang status
        $contributions = ContributionModel::where([
            ['status', $status],
            ['purok', 'purok1']
        ])
            ->with(['memberContribution' => function ($query) {
                $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
            }])
            ->latest('created_at')
            ->get();
        //Bilang san intero na members
        $membersCount = memberModel::count();
        return Inertia::render('collector/report/Index', [
            'contributions' => $contributions,
            'activePurok' => $purok,
            'membersCount' => $membersCount,
            'activeStatus' => $status, // update active status
        ]);
    }
    public function togglePurok($status, $purok)
    {
        // same logic padin sa nauna pero purok na ang in ta toggle o filter
        //pero kukuhaon padin ang default status
        $contributions = ContributionModel::where([
            ['status', $status],
            ['purok', $purok]
        ])
            ->with(['memberContribution' => function ($query) {
                $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
            }])
            ->latest('created_at')
            ->get();
        //Bilang san intero na members
        $membersCount = memberModel::count();
        return Inertia::render('collector/report/Index', [
            'contributions' => $contributions,
            'activePurok' => $purok,
            'membersCount' => $membersCount,
            'activeStatus' => $status, // update active status
        ]);
    }

    public function addCollector(){
        return Inertia::render('admin/dashboard/report/AddCollector');
    }

     // Store collector
    public function storeCollector(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required','string','in:collector'],
            'purok' => ['required','string', Rule::in(['1','2','3','4'])],
        ]);

        // Create the collector in users table
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'purok' => $validated['purok'],
        ]);

        return redirect()->route('reports.addCollector')->with('success', 'Collector added successfully!');
    }

}
