<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryModel;
use App\Models\memberModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function viewMemberInfo($id){ //imudo ang specific member info basi sa id
        $mem = memberModel::with('beneficiaries')->find($id);
        if(!$mem){ //check if member exists return error kapag d mahanap sa database
            return response()->json(['error: ' => 'cannot find member'], 404);
        }
        if(Auth::user()->role == 'collector'){
            return Inertia::render('collector/dashboard/ViewMember', [
            'member' => $mem,
          ]);
        }else if(Auth::user()->role == 'admin'){
            return Inertia::render('admin/dashboard/ViewMember', [
            'member' => $mem,
            ]);
        }
    }
    public function editMember($id){
        $mem = memberModel::with('beneficiaries')->find($id);
        if(!$mem){
            return response()->json(['error: ' => 'cannot find user'], 404);
        }

        return Inertia::render('admin/dashboard/EditMember', [
            'member' => $mem,
        ]);
    }
    public function destroy($id)
    {
        $member = memberModel::findOrFail($id);
        $member->delete(); 

        return redirect()->back()->with('success', 'Member trashed successfully.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $member = memberModel::findOrFail($id);
        $member->status = $request->status;
        $member->save();

        return redirect()->back()->with([
            'message' => 'Member status updated successfully.',
        ]);
    }
}
