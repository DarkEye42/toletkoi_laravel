<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use App\Models\CreditWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Supported providers.
     */
    protected $providers = ['google', 'facebook'];

    /**
     * Redirect to the provider's auth page.
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the provider.
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                    'email_verification_token' => null,
                ]);

                UserProfile::create([
                    'user_id' => $user->id,
                    'avatar' => $socialUser->getAvatar(),
                ]);

                UserPreference::create([
                    'user_id' => $user->id,
                    'language' => 'bn',
                    'currency' => 'BDT',
                ]);

                CreditWallet::create([
                    'user_id' => $user->id,
                    'balance' => 50,
                ]);

                $user->assignRole('tenant');
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard.index'))
                ->with('success', 'Welcome!');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Unable to authenticate with this provider.']);
        }
    }
}
