<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryModel;
use App\Models\memberModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function addNewMember(){
        $bene = BeneficiaryModel::where('user_id', Auth::id())->get();;
        return Inertia::render('admin/dashboard/AddNewMember', [
            'beneficiary' => $bene
        ]);
    }
    public function addMemberPost(Request $request){
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'date_of_birth' => 'required|date|max:255',
            'purok' => 'required|max:255',
            'age' => 'required|integer',
            'middle_name' => 'nullable|string|max:255',
            'status' => 'required|in:Single,Married,Widowed,Divorced,Separated,Live-in,Annulled,active,inactive',
            'occupation' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'beneficiaries' => 'nullable|array',
            'beneficiaries.*.name' => 'required|string|max:255',
            'beneficiaries.*.relation' => 'required|string|max:100',
            'beneficiaries.*.age' => 'required|integer|min:0',
            'beneficiaries.*.birth_date' => 'required|date',
        ]);
       $member = memberModel::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'date_of_birth' => $request->date_of_birth,
            'registration_date' => now(),
            'purok' => $request->purok,
            'age' => $request->age,
            'middle_name' => $request->middle_name,
            'status' => $request->status,
            'occupation' => $request->occupation,
            'gender' => $request->gender,
            'created_at' => now(),
        ]);

        if ($request->has('beneficiaries')) {
        foreach ($request->beneficiaries as $beneficiary) {
            BeneficiaryModel::create([
                'user_id' => $member->id,    
                'name' => $beneficiary['name'],
                'relation' => $beneficiary['relation'],
                'age' => $beneficiary['age'],
                'birth_date' => $beneficiary['birth_date'],
            ]);
        }
    }
        return redirect()->back()->with(['message' => 'member create']);
    }

    public function addBeneficiary(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'age' => 'required',
            'birth_date' => 'required|date'
        ]);
        // if(!$request->validate()){
        //     return response()->json(['error =>' => 'cannot validate']);
        // }
        BeneficiaryModel::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'relation' => $request->relation ?: null,
            'age' => $request->age ?: null,
            'birth_date' => $request->birth_date ?: null,
        ]);

        // $bene = BeneficiaryModel::where('user_id', Auth::id())->get();
        // return Inertia::render('admin/dashboard/AddNewMember', [
        //     'beneficiary' => $bene
        // ]);
    }

    public function deleteBeneficiary($id){
        $bene = BeneficiaryModel::find($id);
        if(!$bene){
            return response()->json(['Error: ' => 'cannot find beneficiary data.']);
        }
        $bene->delete();
    }
     public function newlyRegistered()
    {
        $latestMember = memberModel::orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        if (!$latestMember) {
            return response()->json([
                'message' => 'No member found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Latest member retrieved successfully',
            'data' => $latestMember
        ]);
    }
}
