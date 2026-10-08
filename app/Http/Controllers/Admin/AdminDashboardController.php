<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactUnlock;
use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard with platform-wide figures.
     *
     * Every number here is a real aggregate query; nothing is hardcoded or
     * sampled, so the dashboard stays truthful as the dataset grows.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_listings' => Listing::withoutGlobalScopes()->count(),
            'pending_listings' => Listing::withoutGlobalScopes()->where('status', 'pending')->count(),
            'active_listings' => Listing::withoutGlobalScopes()->where('status', 'active')->count(),
            'total_contact_unlocks' => ContactUnlock::count(),
            'credits_spent' => (int) CreditTransaction::where('type', 'spend')->sum('amount'),
        ];

        // Registrations over the last 14 days for the activity chart.
        $registrations = User::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $registrationSeries = collect(range(13, 0))->mapWithKeys(function ($daysAgo) use ($registrations) {
            $date = now()->subDays($daysAgo)->toDateString();

            return [$date => (int) ($registrations[$date] ?? 0)];
        });

        $recentListings = Listing::withoutGlobalScopes()
            ->with(['user', 'propertyType', 'area'])
            ->latest()
            ->limit(8)
            ->get();

        $recentUsers = User::latest()->limit(8)->get();

        $pendingByType = Listing::withoutGlobalScopes()
            ->where('status', 'pending')
            ->with('propertyType')
            ->get()
            ->groupBy(fn($listing) => $listing->propertyType->name ?? 'Uncategorised')
            ->map->count()
            ->sortDesc();

        return view('admin.dashboard', compact(
            'stats',
            'registrationSeries',
            'recentListings',
            'recentUsers',
            'pendingByType'
        ));
    }
}
