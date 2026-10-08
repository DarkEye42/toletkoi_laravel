<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use App\Models\CreditWallet;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verification_token' => Str::random(60),
        ]);

        // Create user profile
        UserProfile::create([
            'user_id' => $user->id,
            'phone' => $validated['phone'] ?? null,
        ]);

        // Create user preferences
        UserPreference::create([
            'user_id' => $user->id,
            'language' => 'bn',
            'currency' => 'BDT',
        ]);

        // Create credit wallet
        CreditWallet::create([
            'user_id' => $user->id,
            'balance' => 50, // Welcome bonus
        ]);

        // Assign role (find or create with slug to avoid NOT NULL violations)
        $roleName = $validated['role'];
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $role = Role::create([
                'name' => $roleName,
                'slug' => $roleName,
                'guard_name' => 'web',
            ]);
        }
        $user->assignRole($role);

        // Send verification email (queued)
        // Mail::to($user)->send(new VerifyEmail($user));

        // Auto-login
        Auth::login($user);

        // Send welcome notification (queued)
        $user->notify(new WelcomeNotification($user));

        // Redirect to profile completion
        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'Registration successful! Please complete your profile. You received 50 welcome credits.');
    }
}
