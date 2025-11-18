<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReportForCollector extends Controller
{
    public function index()
    {
        $contributions = ContributionModel::with(['memberContribution' => function ($query) {
                $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
            }])
            ->latest('created_at')
            ->get();

        // Bilang san intero na members
         $members = memberModel::all();

        $contributionsIds = ContributionModel::pluck('member_id')->toArray(); 
     $currentDeceasedMembers = DeathReportModel::where('iscurrent', true)
     ->get();
     $currentDeceasedMember = DeathReportModel::where('iscurrent', true)
     ->latest('created_at')
     ->first();
        return Inertia::render('collector/report/Index', [
            'contributions' => $contributions,
            'activePurok' => 'all',
            'membersCount' => $members->count(),
            'activeStatus' => 'all', // wala na status filter, default to "all"
            'contributionsIds' => $contributionsIds,
            'members' => $members,
            'currentDeceasedMembers' => $currentDeceasedMembers,
            'currentDeceasedMember' => $currentDeceasedMember,
        ]);
    }
public function toggleStatus($status, $purok) 
{
    // Filter contributions by MEMBER purok
    $contributions = ContributionModel::whereHas('memberContribution', function ($q) use ($purok) {
            if ($purok !== 'all') {
                $q->where('purok', $purok);
            }
        })
        ->with(['memberContribution' => function ($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'purok', 'contact_number');
        }])
        ->latest('created_at')
        ->get();

    // Count all members
    $membersCount = memberModel::count();

    // All paid members IDs
    $contributionsIds = ContributionModel::pluck('member_id')->toArray(); 

    return Inertia::render('collector/report/Index', [
        'contributions' => $contributions,
        'activePurok' => $purok,
        'membersCount' => $membersCount,
        'activeStatus' => $status,  // now return status properly
        'contributionsIds' => $contributionsIds,
    ]);
}

     public function togglePaid($status = 'paid', $purok) 
    {
        // Removed status filter, only filter by purok
        $contributions = ContributionModel::where('purok', $purok)
            ->with(['memberContribution' => function ($query) {
                $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
            }])
            ->latest('created_at')
            ->get();

        $membersCount = memberModel::count();
        $contributionsIds = ContributionModel::pluck('member_id')->toArray(); 

        return Inertia::render('collector/report/Index', [
            'contributions' => $contributions,
            'activePurok' => $purok,
            'membersCount' => $membersCount,
            'activeStatus' => 'paid', 
            'contributionsIds' => $contributionsIds,
        ]);
    }
public function togglePurok($status, $purok)
{
    // Extract numeric purok value (handles "Purok 1", "purok1", etc)
    $number = preg_replace('/[^0-9]/', '', $purok);

    // Get contributions only for that purok
    $contributions = ContributionModel::where('purok', $purok)
        ->with(['memberContribution' => function ($query) {
            $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
        }])
        ->latest('created_at')
        ->get();

    // Get members correctly
    $members = memberModel::whereRaw(
        "REGEXP_REPLACE(purok, '[^0-9]', '') = ?",
        [$number]
    )->get();

    // Paid IDs only for that purok (important)
    $contributionsIds = ContributionModel::where('purok', $purok)
        ->pluck('member_id')
        ->toArray();

    return Inertia::render('collector/report/Index', [
        'contributions'   => $contributions,
        'activePurok'     => $purok,
        'membersCount'    => $members->count(),
        'activeStatus'    => 'all',
        'contributionsIds'=> $contributionsIds,
        'members'         => $members
    ]);
}



     public function getContributions($id, $purok)
        {
            try {
                $contributions = ContributionModel::where('deceased_id', $id)
                ->where('purok', $purok)
                ->with('memberContribution')
                ->get();

                return response()->json([
                    'contributions' => $contributions
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to fetch contributions.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

}
