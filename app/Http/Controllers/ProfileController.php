<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\OfficialModel;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(){
        $user = User::findOrFail(Auth::id());
        return Inertia::render('collector/dashboard/Profile', [
            'user' => $user
        ]);
    }
     public function editRoute(){
        $user = User::findOrFail(Auth::id());
        return Inertia::render('collector/dashboard/EditProfile', [
            'user' => $user
        ]);
    }
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->back()->with(["message" =>  "Profile updated."]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function presidentProfile()
    {
        // Get the official whose position is 'president'
        $president = OfficialModel::where('position', 'president')->first();

        // Optional: handle case where no president record exists
        if (!$president) {
            return redirect()->back()->with('error', 'No president record found.');
        }

        // Return Inertia page with the president data
        return Inertia::render('admin/dashboard/profile/Index', [
            'president' => $president,
        ]);
    }
}
