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
    public function viewMemberInfo($id){ //imudon ang specific member info basi sa id
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

    public function update(Request $request, $id)
{
    $member = memberModel::findOrFail($id);

    $validated = $request->validate([
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'contact_number' => 'required|string|size:11',
        'address' => 'required|string',
        'purok' => 'required',
        'age' => 'required|integer',
        'gender' => 'required',
        'status' => 'required',
        'occupation' => 'required|string',
        'date_of_birth' => 'required|date',
        'middle_name' => 'nullable|string',
        'beneficiaries' => 'array',
    ]);

    $member->update($validated);

    // Update beneficiaries if provided
    if (isset($validated['beneficiaries'])) {
        $member->beneficiaries()->delete();
        foreach ($validated['beneficiaries'] as $b) {
            $member->beneficiaries()->create($b);
        }
    }

    return redirect()->back()->with('success', 'Member updated successfully!');
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
            'id' => $id,
        ]);
    }

    public function fetchBeneficiary(Request $request, $memberId)
    {
        try {
            
            $beneficiaries = BeneficiaryModel::where('user_id', $memberId)->get();
            
            
            return response()->json([
                'success' => true,
                'data' => $beneficiaries,
                'debug' => [
                    'member_id' => $memberId,
                    'count' => $beneficiaries->count()
                ],
                'message' => 'Beneficiaries fetched successfully'
            ]);
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch beneficiaries',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllMembers()
    {
        $members = memberModel::all();

        return response()->json($members);
    }
    public function search(Request $request)
{
    $query = trim($request->input('query'));

    if (empty($query)) {
        return response()->json(['members' => []]);
    }

    $members = memberModel::query()
        ->where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('middle_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$query}%"])
              ->orWhere('contact_number', 'LIKE', "%{$query}%")
              ->orWhere('purok', 'LIKE', "%{$query}%");
        })
        ->orderBy('first_name', 'asc')
        ->limit(20)
        ->get();

    return response()->json(['members' => $members]);
}
    /**
     * Search members by first_name and last_name
     */
    public function searchMemberContribution(Request $request)
    {
        $request->validate([
            'keyword' => 'nullable|string|max:255',
            'purok' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $keyword = $request->get('keyword', '');
        $selectedPurok = $request->get('purok', '');
        $perPage = $request->get('per_page', 15);

        // Base query
        $query = memberModel::with(['contributions' => function($query) {
            $query->latest()->take(1);
        }])
        ->whereHas('contributions');

        // Search by first_name and last_name
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('middle_name', 'like', "%{$keyword}%");
            });
        }

        // Filter by purok if selected
        if (!empty($selectedPurok)) {
            $query->where('purok', $selectedPurok);
        }

        // Get paginated results
        $members = $query->paginate($perPage);

        return response()->json([
            'member' => $members,
            'search_keyword' => $keyword,
        ]);
    }


}
