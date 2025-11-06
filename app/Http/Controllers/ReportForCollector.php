<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
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
        return Inertia::render('collector/report/Index', [
            'contributions' => $contributions,
            'activePurok' => 'all',
            'membersCount' => $members->count(),
            'activeStatus' => 'all', // wala na status filter, default to "all"
            'contributionsIds' => $contributionsIds,
            'members' => $members,
        ]);
    }

    public function toggleStatus($status, $purok) 
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
            'activeStatus' => 'all', // since we ignore status
            'contributionsIds' => $contributionsIds,
        ]);
    }

   public function togglePurok($status, $purok)
{
    $contributions = ContributionModel::where('purok', $purok)
        ->with(['memberContribution' => function ($query) {
            $query->select('id', 'first_name','middle_name', 'last_name', 'purok', 'contact_number');
        }])
        ->latest('created_at')
        ->get();

    //  Filter members by matching last character of purok
    $lastChar = substr($purok, -1);
    $members = memberModel::whereRaw("RIGHT(purok, 1) = ?", [$lastChar])->get();

    // Collect all paid member IDs
    $contributionsIds = ContributionModel::pluck('member_id')->toArray(); 

    return Inertia::render('collector/report/Index', [
        'contributions'   => $contributions,
        'activePurok'     => $purok,
        'membersCount'    => $members->count(),
        'activeStatus'    => 'all', // since we ignore status
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
