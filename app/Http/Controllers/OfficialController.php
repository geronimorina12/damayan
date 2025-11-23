<?php

namespace App\Http\Controllers;

use App\Models\OfficialModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OfficialController extends Controller
{

public function index() {
    $officials = OfficialModel::all();
    $collectors = User::where('role', 'collector')->get();
    $threshold = now()->subDays(30);

    // Get collectors whose latest log is older than 30 days
    $inactiveCollectorIds = DB::table('users')
        ->select('id', DB::raw('MAX(last_seen_at) as latest_seen'))
        ->groupBy('id')
        ->having('latest_seen', '<=', $threshold)
        ->pluck('id');

    // Update the found collectors
    User::whereIn('id', $inactiveCollectorIds)->update(['status' => 0]);

    $currentYear = Carbon::now()->year;

    // Update official status if term_end equals current year
    $officials->each(function ($official) use ($currentYear) {
        if ($official->term_end && Carbon::parse($official->term_end)->year == $currentYear) {
            $official->status = 0; // boolean false
            $official->save();
        }
    });

    // Combine both into one array (but keep them identifiable)
    $combined = $officials->map(function ($item) {
        $item->type = 'official';
        return $item;
    })->concat(
        $collectors->map(function ($item) {
            $item->type = 'collector';
            return $item;
        })
    )->values(); // reindex the array

    if (Auth::user()->role == 'admin') {
        return Inertia::render('admin/dashboard/official/Home', [
            'officials' => $combined,
        ]);
    } else if (Auth::user()->role == 'collector') {
        return Inertia::render('collector/dashboard/official/Home', [
            'officials' => $combined,
        ]);
    }
}

    public function getInactiveCollectorsCount()
{
    try {
        // Optional debug log

        $inactiveCollectors = User::where('role', 'collector')
            ->where('status', 0)
            ->count();
         $active = User::where('role', 'collector')
            ->where('status', 1)
            ->count();

        return response()->json([
            'inactive_collectors' => $inactiveCollectors,
            'active_collectors' => $active
        ]);

    } catch (\Exception $e) {

        // Log the actual error with stack trace
        Log::error('Failed to fetch inactive collectors count', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Something went wrong while fetching inactive collectors.'
        ], 500);
    }
}

    public function addOfficialRoute(){
        return Inertia::render('admin/dashboard/official/Add');
    }
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:officials,email',
            'position' => 'required|string|max:255',
            'term_start' => 'required|date',
            'term_end' => 'required|date',
            'status' => 'required|boolean',
            'is_override' => 'sometimes|boolean',
        ]);

        if($request->is_override){
            // Delete existing official with the same position
            OfficialModel::where('position', $request->position)->delete();
        }
        OfficialModel::create($request->all());
        return redirect()->back()->with(['success' => 'data created.']);
    }
     public function edit($id){
        $official = OfficialModel::findOrfail($id);
        if(!$official){
            abort(404, 'official not found');
        }
        return Inertia::render('admin/dashboard/official/Edit', [
            'official' => $official
        ]);
     }
     public function viewOfficial($id){
        $official = OfficialModel::findOrfail($id);
        if(!$official){
            abort(404, 'official not found');
        }
        return Inertia::render('admin/dashboard/official/ViewOfficial', [
            'official' => $official
        ]);
     }
    public function editData(Request $request, $id)
{
    $official = OfficialModel::findOrFail($id);

    if (!$official) {
        abort(404, 'Official not found');
    }

    //  Combine first, middle, and last name into one
    $request->merge([
        'name' => trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name)
    ]);

    //  Validate combined data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'term_start' => 'required|date',
        'term_end' => 'required|date',
        'status' => 'required|boolean',
        'email' => 'nullable|email',
    ]);

    $official->update($validated);

    return redirect()->back()->with('success', 'Official updated successfully.');
}

public function editCollector(Request $request, $id)
{
    $official = User::findOrFail($id);

    if (!$official) {
        abort(404, 'Official not found');
    }

    //  Combine first, middle, and last name into one
    $request->merge([
        'name' => trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name)
    ]);

    //  Validate combined data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'term_start' => 'required|date',
        'term_end' => 'required|date',
        'status' => 'required|boolean',
        'email' => 'nullable|email',
    ]);

    $official->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'created_at' => $validated['term_start'],
        'status' => $validated['status'],
        'term_start' => $validated['term_start'],
        'term_end' => $validated['term_end'],
    ]);
    $official->save();
    Log::info(['official: ' => $official]);
    Log::info(['validate: ' => $validated]);
    return redirect()->back()->with('success', 'Official updated successfully.');
}

    public function delete($id){
        $official = OfficialModel::find($id);
        if(!$official){
            $collector = User::findOrFail($id);
            $collector->delete();
            return redirect()->back()->with(['success' => 'collector deleted!'], 201);
        }
        $official->delete();    
        return redirect()->back()->with(['success' => 'official deleted!'], 201);
    }
    public function hasPresident(){
        $hasPresident = OfficialModel::where('position', 'President')->exists();

        return response()->json([
            'has_president' => $hasPresident
        ]);
    }
    public function toggleStatus($id){
        Log::info(['official id: ' => $id]);
        $official = OfficialModel::findOrFail($id);
        Log::info(['official: ' => $official]);
        $official->update([
            'status' => !$official->status
        ]);
    }

    public function officialCount(){
        $secretaryCount = OfficialModel::where('position', 'secretary')->count();
        $treasurerCount = OfficialModel::where('position', 'treasurer')->count();
        $visePresidentCount = OfficialModel::where('position', 'vice_president')->count();
        $auditorCount = OfficialModel::where('position', 'auditor')->count();

        $data = [
            'secretary_count' => $secretaryCount,
            'treasurer_count' => $treasurerCount,
            'vice_president_count' => $visePresidentCount,
            'auditor_count' => $auditorCount,
        ];
        return response()->json([
            'data' => $data
        ]);
    }
}
