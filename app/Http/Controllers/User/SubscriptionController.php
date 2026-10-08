<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();
        $currentSubscription = Auth::user()->activeSubscription;

        return view('dashboard.subscription', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        if (!$plan->is_active) {
            return back()->withErrors(['error' => 'This plan is no longer available.']);
        }

        $user = Auth::user();

        // Cancel existing subscription
        if ($user->activeSubscription) {
            $user->activeSubscription->update(['status' => 'cancelled', 'ends_at' => now()]);
        }

        // Create new subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'auto_renew' => true,
        ]);

        return back()->with('success', "Successfully subscribed to {$plan->name} plan!");
    }

    public function cancel()
    {
        $subscription = Auth::user()->activeSubscription;

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'auto_renew' => false,
                'ends_at' => now(),
            ]);
        }

        return back()->with('success', 'Subscription cancelled successfully.');
    }
}
