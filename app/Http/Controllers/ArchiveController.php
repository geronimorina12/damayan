<?php

namespace App\Http\Controllers;

use App\Models\DeathReportModel;
use App\Models\memberModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index(){
        $members = memberModel::onlyTrashed()->get();
        $deceasedMembers = DeathReportModel::select('report_id', 'deceased_name', 'date_of_death')->get();
        return Inertia::render('admin/dashboard/archive/member/Home', [
            'members' => $members,
            'deceasedMembers' => $deceasedMembers
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
}
