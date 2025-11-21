<?php

namespace App\Http\Controllers;

use App\Models\OfficialModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $admins = User::all();   
        $users = OfficialModel::all();
        return Inertia::render('admin/roleManagementPage/Index', [
            'users' => $users,
            'admins' => $admins,
        ]);
    }
    public function edit(User $user)
    {
        return Inertia::render('admin/roleManagementPage/Edit', [
            'user' => $user
        ]);
    }
    public function viewUser(User $user)
    {
        return Inertia::render('admin/roleManagementPage/View', [
            'user' => $user
        ]);
    }
      public function editOfficial(OfficialModel $official)
    {
        return Inertia::render('admin/roleManagementPage/EditOfficial', [
            'official' => $official
        ]);
    }
     public function viewOfficial(OfficialModel $official)
    {
        return Inertia::render('admin/roleManagementPage/ViewOfficial', [
            'official' => $official
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

     public function updateOfficial(Request $request, OfficialModel $official)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:officials,email,' . $official->id,
            'position' => 'required|',
            'password' => 'nullable|string|min:6',
        ]);

        if($validated['position'] === 'president'){
        $official->name = $validated['name'];
        $official->email = $validated['email'];
        $official->position = $validated['position'];

        // Update also the admin credentials since c president mismo ang admin
        $admin = User::where('role', 'admin')->first();
        $admin->update([
            'name' => $validated['name'] ?: $admin->name,
            'email' => $validated['email'] ?: $admin->email,
            'password' => $validated['password'] ?: $admin->password,
        ]);
        $official->save();
        $admin->save();
        return redirect()->back()->with('success', 'User updated successfully.');

        }

        $official->name = $validated['name'];
        $official->email = $validated['email'];
        $official->position = $validated['position'];

        if (!empty($validated['password'])) {
            $official->password = Hash::make($validated['password']);
        }

        $official->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function add(){
        return Inertia::render('admin/roleManagementPage/Add');
    }
    public function addUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|',
            'password' => 'required|string|min:6',
            'purok' => 'required|in:purok,1,2,3,4',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'purok' => $validated['purok']
        ]);

        return redirect()->back()->with('success', 'User added successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json(['error' => 'account not found.']);
        }else if($user->id == Auth::id()){
            return response()->json(['error' => 'You cannot delete you own account.']);
        }
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function viewSpecificRole($role)
    {
        
        if($role == 'vise president') $role = 'vise_president';
        if($role == 'purok leader') $role = 'purok_leader';
        $users = User::where('role', $role)->get();
        if($users->isEmpty()) {
           $users = OfficialModel::where('position', $role)->get();
        }
        return Inertia::render('admin/roleManagementPage/ViewSpecificRole', [
            'users' => $users,
            'role' => $role,
        ]);
    }
}
