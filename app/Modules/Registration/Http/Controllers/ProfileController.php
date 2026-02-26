<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Registration\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show profile page
     */
    public function show()
    {
        $user = auth()->user()->load('profile');
        
        $statistics = [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_events' => $user->bookings()
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '>', now());
                })->count(),
            'total_spent' => $user->payments()->sum('amount'),
            'member_since' => $user->created_at->format('F Y')
        ];

        return view('registration::profile', compact('user', 'statistics'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = auth()->user()->load('profile');
        return view('registration::profile-edit', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        // Update user basic info
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        // Update profile
        $user->profile()->update([
            'phone' => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'bio' => $request->bio,
            'preferences' => $request->preferences,
            'social_links' => $request->social_links
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile()->update(['profile_photo' => $path]);
        }

        activity()
            ->performedOn($user)
            ->log('Profile updated');

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed|different:current_password'
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        activity()
            ->performedOn($user)
            ->log('Password changed');

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Delete account
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = auth()->user();
        
        // Logout
        Auth::logout();
        
        // Delete user (soft delete)
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}