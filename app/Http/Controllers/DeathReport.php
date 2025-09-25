<?php

namespace App\Http\Controllers;

use App\Models\DeathReportModel;
use App\Models\memberModel;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use Inertia\Inertia;

class DeathReport extends Controller
{
    public function undo($id)
    {
        $member = memberModel::withTrashed()->find($id);
        $deceasedMember = DeathReportModel::where('member_id', $id)->first();

        if ($member) {
            $member->restore();
        }
        if($deceasedMember) {
            $deceasedMember->delete();
        }
        return Inertia::location(url()->previous());

    }
    public function deletePermanently($id)
    {
        $deceasedMember = DeathReportModel::where('member_id', $id)->first();
        $memberWithThatId = memberModel::where('id', $id)->first();
        if ($deceasedMember) {
            $deceasedMember->delete();
        }
        if ($memberWithThatId) {
            $memberWithThatId->forceDelete();
        }
        return redirect()->back()->with(['message' => 'Member deleted permanently.'], 200);
    }
}
