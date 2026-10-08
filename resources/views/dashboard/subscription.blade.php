@extends('layouts.dashboard')
@section('title', 'Subscription')
@section('header', 'Subscription Plans')

@section('content')
<div class="space-y-6">
    {{-- Current Subscription --}}
    @if($currentSubscription)
        <div class="rounded-2xl bg-gradient-to-r from-primary to-secondary p-6 text-white shadow-lg shadow-primary/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                        <i class="material-icons">workspace_premium</i>
                    </div>
                    <div>
                        <p class="text-xs text-white/80 mb-0">Current Plan</p>
                        <p class="text-lg font-bold mb-0">{{ $currentSubscription->plan->name ?? 'N/A' }}</p>
                        <p class="text-xs text-white/80 mb-0">
                            Valid: {{ $currentSubscription->starts_at?->format('M j') }} – {{ $currentSubscription->ends_at?->format('M j, Y') }}
                            @if($currentSubscription->auto_renew) · Auto-renewing @endif
                        </p>
                    </div>
                </div>
                <form action="{{ route('dashboard.subscription.cancel') }}" method="POST" onsubmit="return confirm('Cancel your subscription?')">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-white/20 px-5 py-2.5 text-xs font-bold text-white hover:bg-white/30 transition-colors">Cancel Subscription</button>
                </form>
            </div>
        </div>
    @endif
    {{-- Available Plans --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $plan)
            @php $isCurrent = $currentSubscription?->plan_id === $plan->id; @endphp
            <div class="card {{ $isCurrent ? 'border-primary shadow-md shadow-primary/10' : '' }}">
                <div class="card-body p-6 flex-col h-full">
                    <h3 class="text-base font-bold text-dark">{{ $plan->name }}</h3>
                    <div class="mt-3 flex items-end gap-1">
                        <span class="text-2xl font-extrabold text-primary">৳{{ number_format((float) $plan->price_monthly) }}</span>
                        <span class="text-[11px] text-muted font-medium mb-1">/ month</span>
                    </div>

                    <ul class="mt-4 space-y-2 flex-1">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <i class="material-icons text-success text-[15px]">check_circle</i>
                            {{ $plan->max_listings }} listing{{ $plan->max_listings == 1 ? '' : 's' }}
                        </li>
                        @foreach((array) $plan->features as $feature)
                            <li class="flex items-center gap-2 text-xs text-gray-600">
                                <i class="material-icons text-success text-[15px]">check_circle</i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if(! $isCurrent)
                        <form action="{{ route('dashboard.subscription.subscribe', $plan) }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="w-full btn {{ (float) $plan->price_monthly == 0.0 ? 'btn-light' : 'btn-primary' }}">
                                {{ (float) $plan->price_monthly == 0.0 ? 'Downgrade to Free' : 'Subscribe' }}
                            </button>
                        </form>
                    @else
                        <div class="mt-6 w-full rounded-xl bg-primary/10 px-4 py-2.5 text-center text-xs font-bold text-primary">Current Plan</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 card">
                <div class="card-body p-10 text-center">
                    <i class="material-icons text-gray-300 text-4xl">sell</i>
                    <p class="text-sm text-muted mt-2 mb-0">No subscription plans are available right now.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
