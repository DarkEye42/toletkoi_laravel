@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
@php
    $statTiles = [
        ['label' => 'Active Listings', 'value' => $stats['active_listings'], 'icon' => 'home', 'color' => 'bg-[#ecf2ff] text-[#5d87ff]'],
        ['label' => 'Pending Review', 'value' => $stats['pending_listings'], 'icon' => 'hourglass_empty', 'color' => 'bg-[#fef5e5] text-[#ffae1f]'],
        ['label' => 'Favorite Homes', 'value' => $stats['favorites_count'], 'icon' => 'favorite', 'color' => 'bg-[#fbf2ef] text-[#fa896b]'],
        ['label' => 'Contacts Unlocked', 'value' => $stats['contact_unlocks'], 'icon' => 'lock_open', 'color' => 'bg-[#e6fffa] text-[#13deb9]'],
    ];
@endphp
<div class="space-y-6">

    {{-- Welcome --}}
    <div class="card">
        <div class="card-body">
            <h4 class="text-xl font-bold text-dark">Welcome back, {{ $user->name }}</h4>
            <p class="text-sm text-muted mt-1 mb-0">Here's what's happening with your properties today.</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($statTiles as $tile)
            <div class="card">
                <div class="card-body p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $tile['color'] }}">
                            <i class="material-icons">{{ $tile['icon'] }}</i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-dark">{{ number_format($tile['value']) }}</h3>
                            <p class="text-sm text-muted">{{ $tile['label'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Subscription Status --}}
    @if($user->activeSubscription)
        <div class="rounded-2xl bg-gradient-to-r from-primary to-secondary p-6 text-white shadow-lg shadow-primary/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                        <i class="material-icons">workspace_premium</i>
                    </div>
                    <div>
                        <p class="text-xs text-white/80 mb-0">Current Plan</p>
                        <p class="text-lg font-bold mb-0">{{ $user->activeSubscription->plan->name ?? 'N/A' }}</p>
                        <p class="text-xs text-white/80 mb-0">Valid until {{ $user->activeSubscription->ends_at?->format('M j, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
                <a href="{{ route('dashboard.subscription.index') }}" class="rounded-2xl bg-white/20 px-5 py-2.5 text-xs font-bold text-white hover:bg-white/30 transition-colors text-center">
                    Manage Plan
                </a>
            </div>
        </div>
    @endif
    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach([
            ['route' => 'dashboard.listings.create', 'icon' => 'add_home', 'title' => 'New Listing', 'desc' => 'Post a new property'],
            ['route' => 'dashboard.credits.index', 'icon' => 'account_balance_wallet', 'title' => 'Buy Credits', 'desc' => 'Top up your wallet'],
            ['route' => 'dashboard.koifind.index', 'icon' => 'travel_explore', 'title' => 'KoiFind', 'desc' => 'Let agents find properties for you'],
        ] as $action)
            <a href="{{ route($action['route']) }}" class="card card-hover group">
                <div class="card-body p-6">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <i class="material-icons text-primary">{{ $action['icon'] }}</i>
                    </div>
                    <h3 class="mt-3 font-bold text-sm text-dark group-hover:text-primary transition-colors">{{ $action['title'] }}</h3>
                    <p class="text-xs text-muted mb-0">{{ $action['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Recent Listings --}}
    @if($recentListings->count())
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h5 class="text-base font-bold text-dark">Recent Listings</h5>
                <a href="{{ route('dashboard.listings.index') }}" class="text-primary text-xs font-semibold hover:text-primary-dark transition-colors">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentListings as $listing)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if($listing->images->count())
                                            <img src="{{ Storage::disk('public')->url($listing->images->first()->path) }}" alt="{{ $listing->title }}" class="h-10 w-10 rounded-lg object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-primary/5 flex items-center justify-center">
                                                <i class="material-icons text-primary/40 text-[18px]">apartment</i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-dark text-sm mb-0">{{ Str::limit($listing->title, 30) }}</p>
                                            <p class="text-[11px] text-muted mb-0">{{ $listing->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $listing->propertyType->name ?? '-' }}</td>
                                <td class="font-bold text-dark">৳{{ number_format($listing->price) }}</td>
                                <td>
                                    <span class="badge {{ $listing->status === 'active' ? 'badge-success' : ($listing->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ ucfirst($listing->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.listings.edit', $listing) }}" class="text-primary font-semibold hover:text-primary-dark transition-colors">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
