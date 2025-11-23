<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\User;
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
    // Normalize purok input
    $clean = strtolower(trim((string)$purok));
    $isAll = $clean === 'all';

    // Convert purok4 → Purok 4 (DB format)
    $purokFormatted = null;
    if (!$isAll) {
        $purokFormatted = preg_replace('/(purok)\s*(\d+)/i', 'Purok $2', $clean);
        $purokFormatted = ucwords($purokFormatted);
    }

    // Get contributions filtered by member's purok
    $contributions = ContributionModel::whereHas('memberContribution', function ($q) use ($isAll, $purokFormatted) {
            if (!$isAll && $purokFormatted) {
                $q->where('purok', $purokFormatted);
            }
        })
        ->with(['memberContribution' => function ($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'purok', 'contact_number');
        }])
        ->latest('created_at')
        ->get();

    // Filter members too (consistent with toggle() and toggleDeceased())
    $membersQuery = memberModel::orderBy('first_name', 'asc');

    if (!$isAll && $purokFormatted) {
        $membersQuery->where('purok', $purokFormatted);
    }

    $members = $membersQuery->get();  // Always return array, no paginator

    // Get IDs of paid members
    $contributionsIds = ContributionModel::pluck('member_id')->toArray();

    // Count all members (not filtered)
    $membersCount = memberModel::count();

    return Inertia::render('collector/report/Index', [
        'contributions' => $contributions,
        'members' => $members,                // <-- Added members!
        'activePurok' => $purokFormatted ?? $purok,
        'activeStatus' => $status,
        'membersCount' => $membersCount,
        'contributionsIds' => $contributionsIds,
    ]);
}

public function toggleDeceased($id, $purok)
{
    Log::info("Toggling deceased for ID: $id and Purok: $purok");

    // Normalize purok input
    $clean = strtolower(trim((string)$purok));
    $isAll = $clean === 'all';

    // Build DB format only when not "all"
    $purokFormatted = null;
    if (!$isAll) {
        // e.g. "purok2" or "purok 2" -> "Purok 2"
        $purokFormatted = preg_replace('/(purok)\s*(\d+)/i', 'Purok $2', $clean);
        $purokFormatted = ucwords($purokFormatted);
    }

    Log::info("isAll: " . ($isAll ? 'true' : 'false') . ", purokFormatted: " . ($purokFormatted ?? 'null'));

    // Members: include contributions for this deceased_id, AND filter members by purok if not ALL
    $membersQuery = memberModel::with(['contributions' => function ($q) use ($id) {
        $q->where('deceased_id', $id);
    }])->orderBy('first_name', 'asc');

    if (!$isAll && $purokFormatted) {
        $membersQuery->where('purok', $purokFormatted);
    }

    $mem = $membersQuery->get(); // returns only members matching the purok when applicable

    // Contributions: filter contributions for this deceased. If not ALL, only contributions whose member has that purok
    $contributionsQuery = ContributionModel::where('deceased_id', $id)
        ->whereHas('memberContribution', function ($q) use ($isAll, $purokFormatted) {
            if (!$isAll && $purokFormatted) {
                $q->where('purok', $purokFormatted);
            }
        })
        ->with(['memberContribution' => function ($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'purok', 'contact_number');
        }])
        ->latest('created_at');

    $contributions = $contributionsQuery->get();
    Log::info("Fetched " . $contributions->count() . " contributions for deceased ID: $id");
    $collectors = User::select('id', 'name', 'purok')->where('role', 'collector')->get();

    $currentDeceasedMembers = DeathReportModel::where('iscurrent', true)->get();

    $currentDeceasedMember = DeathReportModel::where('member_id', $id)
        ->latest('created_at')
        ->first();

    return Inertia::render('collector/report/Index', [
        'contributions' => $contributions,
        'members' => $mem,
        // Keep activePurok consistent with what your frontend expects.
        // If frontend expects the DB format ("Purok 2"), send $purokFormatted; if it expects the raw input, send $purok.
        'activePurok' => $purokFormatted ?? $purok,
        'activeStatus' => 'all',
        'currentDeceasedMembers' => $currentDeceasedMembers,
        'currentDeceasedMember' => $currentDeceasedMember ?: null,
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
public function togglePurok($status, $purok, $deceasedId)
{
    
    $dead = DeathReportModel::where('member_id' , $deceasedId);
    if (!$dead) {
        dd("Deceased member not found." . $deceasedId);
    }
    // Extract numeric purok value (handles "Purok 1", "purok1", etc)
    $number = preg_replace('/[^0-9]/', '', $purok);

    // Get contributions only for that purok
    $contributions = ContributionModel::where('purok', $purok)
        ->where('deceased_id', $deceasedId)
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
    ->where('deceased_id', $deceasedId)
        ->pluck('member_id')
        ->toArray();

$currentDeceasedMembers = DeathReportModel::where('iscurrent', true)->get();
    
    // Fix: Handle null case
    $currentDeceasedMember = DeathReportModel::where('member_id', $deceasedId)

        ->latest('created_at')
        ->first();
    return Inertia::render('collector/report/Index', [
        'contributions'   => $contributions,
        'activePurok'     => $purok,
        'membersCount'    => $members->count(),
        'activeStatus'    => 'all',
        'contributionsIds'=> $contributionsIds,
        'members'         => $members,
        'currentDeceasedMembers' => $currentDeceasedMembers,
        'currentDeceasedMember' => $currentDeceasedMember,
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
