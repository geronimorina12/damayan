<?php

namespace App\Http\Controllers;

use App\Models\DeathReportModel;
use App\Models\memberModel;
use App\Models\OfficialModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index(){
        $members = memberModel::onlyTrashed()->get();
        $deceasedMembers = DeathReportModel::select('report_id', 'member_id', 'deceased_name', 'date_of_death')
        ->orderBy('date_of_death', 'desc')
        ->get();
        $officials = OfficialModel::onlyTrashed()->get();

        return Inertia::render('admin/dashboard/archive/member/Home', [
            'members' => $members,
            'deceasedMembers' => $deceasedMembers,
            'officials' => $officials,
        ]);
    }
    public function view($id){
        $member = memberModel::onlyTrashed()->with('beneficiaries')->findOrFail($id);
        return Inertia::render('admin/dashboard/archive/member/View', [
            'member' => $member
        ]);
    }
    public function deletePermanently($id){
        $member = memberModel::onlyTrashed()->with('beneficiaries')->findOrFail($id);
        $member->forceDelete();
        return redirect()->back()->with(['success' => 'Member deleted successfully...'], 201);
    }
    public function undo($id)
    {
        $member = memberModel::onlyTrashed()->findOrFail($id);
        $member->restore();
        return redirect()->back()->with(['success' => 'Member restored successfully...'], 201);
    }
}
