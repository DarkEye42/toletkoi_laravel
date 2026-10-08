<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load('profile', 'preferences');

        $divisions = Division::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $areas = Area::orderBy('name')->get();

        return view('dashboard.profile.edit', compact('user', 'divisions', 'districts', 'areas'));
    }

    /**
     * Update user profile.
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Verify current password if changing password (already handled in FormRequest)
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update user basics
        if ($request->has('name') || $request->has('email')) {
            $user->update($request->only('name', 'email'));
        }

        // Update or create profile
        $profileData = $request->only([
            'phone', 'nid_number', 'bio', 'address',
            'company_name', 'website', 'facebook', 'twitter', 'linkedin',
            'division_id', 'district_id', 'area_id',
        ]);

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'language' => 'sometimes|in:bn,en',
            'currency' => 'sometimes|in:BDT,USD',
            'theme' => 'sometimes|in:light,dark,system',
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
        ]);

        $prefsData = $request->only([
            'language', 'currency', 'theme',
            'email_notifications', 'sms_notifications', 'push_notifications',
        ]);

        if ($user->preferences) {
            $user->preferences->update($prefsData);
        } else {
            $user->preferences()->create($prefsData);
        }

        return back()->with('success', 'Preferences updated successfully!');
    }
}
