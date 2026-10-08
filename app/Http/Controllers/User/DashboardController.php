<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Favorite;
use App\Models\ContactUnlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('profile', 'activeSubscription.plan');

        $stats = [
            'active_listings' => Listing::where('user_id', $user->id)
                ->where('status', 'active')->count(),
            'pending_listings' => Listing::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'favorites_count' => Favorite::where('user_id', $user->id)->count(),
            'contact_unlocks' => ContactUnlock::where('user_id', $user->id)->count(),
        ];

        $recentListings = Listing::where('user_id', $user->id)
            ->with('images')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('user', 'stats', 'recentListings'));
    }
}
