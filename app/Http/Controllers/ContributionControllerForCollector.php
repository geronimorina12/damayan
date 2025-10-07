<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use App\Models\memberModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContributionControllerForCollector extends Controller
{
    public function index()
    {
         $mem = memberModel::with('contributions')->get();
         $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();
        $paidMembersId = ContributionModel::pluck('member_id')->toArray();
        $currentCollector = User::where('id', Auth::id())->get();
        return Inertia::render('collector/contribution/MemberContribution', [
            'member' => $mem,
            'selectedPurok' => 'all',
            'collectors' => $collectors,
            'paidMembersId' => $paidMembersId,
            'currentCollector', $currentCollector,
        ]);
    }

  public function toggleContributionPurok($purok){
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
    ->with('contributions')->get();

    if ($mem->isEmpty() && $purok === 'all') {
        $mem = memberModel::with('contributions')->get();
    }

    $collectors = User::select('id', 'name', 'purok')
        ->where('role', 'collector')
        ->get();

        $paidMembersId = ContributionModel::pluck('member_id')->toArray();
        return Inertia::render('collector/contribution/MemberContribution', [
            'member' => $mem,
            'selectedPurok' => $purok,
            'paidMembersId' => $paidMembersId,
        ]);
    }

    public function deleteContribution($id){
        $contribution = ContributionModel::where('member_id', $id)->first();
        if ($contribution) {
            $contribution->delete();
        }
        return redirect()->back()->with(['success' => 'Contribution deleted successfully.']);
    }
}
