<?php

namespace App\Http\Controllers;

use App\Models\OfficialModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OfficialController extends Controller
{
    public function index(){
        $officials = OfficialModel::all();
        if(Auth::user()->role == 'admin'){
            return Inertia::render('admin/dashboard/official/Home', [
            'officials' => $officials,
            ]);
        }else if(Auth::user()->role == 'collector'){
            return Inertia::render('collector/dashboard/official/Home', [
            'officials' => $officials,
            ]);
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
            'status' => 'required|boolean'
        ]);

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
     public function editData(Request $request,  $id){
        $official = OfficialModel::findOrfail($id);
        if(!$official){
            abort(404, 'official not found');
        }
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'term_start' => 'required|date',
            'term_end' => 'required|date',
            'status' => 'required|boolean'
        ]);
        $official->update($validated);
        return redirect()->back()->with('success', 'Official updated successfully.');
    }
    public function delete($id){
        $official = OfficialModel::findOrFail($id);
        if(!$official){
            abort(404, "official not found");
        }
        $official->delete();    
        return redirect()->back()->with(['success' => 'official deleted!'], 201);
        // TODO ma kadto sa archieve 
    }
}
