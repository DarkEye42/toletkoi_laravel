@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
    @php
        $tiles = [
            [
                'label' => 'Total Users',
                'value' => $stats['total_users'],
                'icon' => 'group',
                'color' => 'bg-[#ecf2ff] text-[#5d87ff]',
                'route' => null,
            ],
            [
                'label' => 'Active Listings',
                'value' => $stats['active_listings'],
                'icon' => 'home_work',
                'color' => 'bg-[#e6fffa] text-[#13deb9]',
                'route' => route('admin.listings.index', ['status' => 'active']),
            ],
            [
                'label' => 'Pending Review',
                'value' => $stats['pending_listings'],
                'icon' => 'pending_actions',
                'color' => 'bg-[#fef5e5] text-[#ffae1f]',
                'route' => route('admin.listings.index', ['status' => 'pending']),
            ],
            [
                'label' => 'Contact Unlocks',
                'value' => $stats['total_contact_unlocks'],
                'icon' => 'lock_open',
                'color' => 'bg-[#fbf2ef] text-[#fa896b]',
                'route' => null,
            ],
        ];
        $maxRegistrations = max(1, $registrationSeries->max());
    @endphp

    <div class="space-y-6">

        {{-- Attention banner --}}
        @if ($stats['pending_listings'] > 0)
            <div
                class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-2xl bg-gradient-to-r from-primary to-secondary p-5 text-white shadow-lg shadow-primary/20">
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <i class="material-icons">pending_actions</i>
                    </div>
                    <div>
                        <p class="text-sm font-bold mb-0">{{ $stats['pending_listings'] }}
                            listing{{ $stats['pending_listings'] == 1 ? '' : 's' }} awaiting review</p>
                        <p class="text-xs text-white/80 mb-0">Approve or reject submissions to keep the marketplace current.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}"
                    class="shrink-0 rounded-xl bg-white/20 hover:bg-white/30 px-5 py-2.5 text-xs font-bold text-white transition-colors text-center">
                    Review now
                </a>
            </div>
        @endif

        {{-- Stat tiles --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($tiles as $tile)
                @php $Tag = $tile['route'] ? 'a' : 'div'; @endphp
                <{{ $Tag }} @if ($tile['route']) href="{{ $tile['route'] }}" @endif
                    class="card {{ $tile['route'] ? 'card-hover' : '' }}">
                    <div class="card-body p-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $tile['color'] }}">
                                <i class="material-icons">{{ $tile['icon'] }}</i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-dark mb-0">{{ number_format($tile['value']) }}</h3>
                                <p class="text-sm text-muted mb-0">{{ $tile['label'] }}</p>
                            </div>
                        </div>
                    </div>
                    </{{ $Tag }}>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Registration chart --}}
            <div class="card lg:col-span-2">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h5 class="text-base font-bold text-dark mb-0">New Registrations</h5>
                        <p class="text-xs text-muted mb-0">Last 14 days</p>
                    </div>
                    <span class="badge badge-primary">{{ $registrationSeries->sum() }} total</span>
                </div>
                <div class="card-body">
                    <div class="flex items-end gap-1.5 h-40">
                        @foreach ($registrationSeries as $date => $count)
                            @php $height = max(4, (int) round(($count / $maxRegistrations) * 100)); @endphp
                            <div class="flex-1 flex-col items-center justify-end h-full group"
                                title="{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}: {{ $count }}">
                                <span
                                    class="text-[9px] font-bold text-muted mb-1 opacity-0 group-hover:opacity-100 transition-opacity">{{ $count }}</span>
                                <div class="w-full rounded-t bg-primary/80 group-hover:bg-primary transition-colors"
                                    style="height: {{ $height }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-2 text-[9px] text-gray-400 font-medium">
                        <span>{{ \Carbon\Carbon::parse($registrationSeries->keys()->first())->format('M d') }}</span>
                        <span>Today</span>
                    </div>
                </div>
            </div>

            {{-- Pending breakdown --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="text-base font-bold text-dark mb-0">Pending by Type</h5>
                </div>
                <div class="card-body">
                    @forelse($pendingByType as $type => $count)
                        <div
                            class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <span class="text-xs font-medium text-gray-600">{{ $type }}</span>
                            <span class="badge badge-warning">{{ $count }}</span>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="material-icons text-success text-3xl">task_alt</i>
                            <p class="text-xs text-muted mt-2 mb-0">Nothing awaiting review.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Recent listings --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h5 class="text-base font-bold text-dark mb-0">Recent Listings</h5>
                    <a href="{{ route('admin.listings.index') }}"
                        class="text-primary text-xs font-semibold hover:text-primary-dark transition-colors">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Listing</th>
                                <th>Owner</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentListings as $listing)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.listings.show', $listing) }}"
                                            class="font-semibold text-dark text-xs hover:text-primary transition-colors line-clamp-1">
                                            {{ Str::limit($listing->title, 34) }}
                                        </a>
                                        <div class="text-[10px] text-muted">{{ $listing->propertyType->name ?? '—' }}</div>
                                    </td>
                                    <td class="text-muted text-xs">{{ Str::limit($listing->user->name ?? '—', 16) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $listing->status === 'active' ? 'badge-success' : ($listing->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                            {{ ucfirst($listing->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-xs text-muted py-6">No listings yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent users --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="text-base font-bold text-dark mb-0">Newest Users</h5>
                </div>
                <div class="card-body">
                    @forelse($recentUsers as $recent)
                        <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <div
                                class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs uppercase shrink-0">
                                {{ substr($recent->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-dark truncate mb-0">{{ $recent->name }}</p>
                                <p class="text-[10px] text-muted truncate mb-0">{{ $recent->email }}</p>
                            </div>
                            <span
                                class="text-[10px] text-gray-400 shrink-0">{{ $recent->created_at->diffForHumans(null, true) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-muted text-center py-4 mb-0">No users yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue summary --}}
        <div class="card">
            <div class="card-body p-6 flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i class="material-icons text-primary">payments</i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-0">Credits spent on contact unlocks</p>
                        <p class="text-xl font-black text-dark mb-0">{{ number_format($stats['credits_spent']) }}</p>
                    </div>
                </div>
                <span class="badge badge-secondary self-start sm:self-center">Ledger total</span>
            </div>
        </div>
    </div>
@endsection
