<?php

namespace App\Http\Controllers;

use App\Models\OfficialModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OfficialArchive extends Controller
{
     public function index(){
        $officials = OfficialModel::onlyTrashed()->get();
        return Inertia::render('admin/dashboard/archive/official/Home', [
            'officials' => $officials
        ]);
    }
    public function view($id){
        $official = OfficialModel::onlyTrashed()->findOrFail($id);
        return Inertia::render('admin/dashboard/archive/official/View', [
            'official' => $official
        ]);
    }
    public function deletePermanently($id){
        $member = OfficialModel::onlyTrashed()->findOrFail($id);
        $member->forceDelete();
        return redirect()->back()->with(['success' => 'Member deleted successfully...'], 201);
    }
    public function restoreOfficial($id){
        $official = OfficialModel::onlyTrashed()->findOrFail($id);
        $official->restore();
        return redirect()->back()->with(['success' => 'Official restored successfully...'], 201);
    }
    public function restoreCollector($id)
        {
            $collector = User::onlyTrashed()
                ->where('role', 'collector')
                ->where('id', $id)
                ->firstOrFail();
            Log::info('id: '. $collector->id);
            $collector->restore();

            return redirect()->back()->with('success', 'Collector restored successfully.');
        }
}
