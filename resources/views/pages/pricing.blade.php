@extends('layouts.app')

@section('title', 'Pricing - ToLetKoi')
@section('description', 'Choose the right plan for your property needs. Free listings, premium plans, and credit packages available.')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-dark">Pricing Plans</h1>
        <p class="text-sm text-gray-400 mt-2">Choose the plan that fits your needs. Start free, upgrade anytime.</p>
    </div>

    {{-- Subscription Plans --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $index => $plan)
            @php $isPopular = $index === 2; @endphp
            <div class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-primary shadow-lg shadow-primary/10' : 'border-gray-100' }} p-6 flex flex-col">
                @if($isPopular)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-primary px-3 py-1 text-[10px] font-bold text-white uppercase tracking-wide shadow-sm">Most Popular</span>
                @endif

                <h3 class="text-base font-bold text-dark">{{ $plan->name }}</h3>
                @if($plan->description)
                    <p class="text-[11px] text-gray-400 mt-1 mb-0">{{ $plan->description }}</p>
                @endif

                <div class="mt-4 flex items-end gap-1">
                    <span class="text-3xl font-extrabold text-primary">৳{{ number_format((float) $plan->price_monthly) }}</span>
                    <span class="text-[11px] text-gray-400 font-medium mb-1.5">/ month</span>
                </div>
                @if($plan->price_yearly)
                    <p class="text-[11px] text-gray-400 mt-1 mb-0">
                        or ৳{{ number_format((float) $plan->price_yearly) }} / year
                    </p>
                @endif

                <ul class="mt-5 space-y-2.5 flex-1">
                    <li class="flex items-start gap-2 text-xs text-gray-600">
                        <i class="material-icons text-success text-[15px] shrink-0">check_circle</i>
                        <span>{{ $plan->max_listings }} listing{{ $plan->max_listings == 1 ? '' : 's' }}</span>
                    </li>
                    <li class="flex items-start gap-2 text-xs text-gray-600">
                        <i class="material-icons text-success text-[15px] shrink-0">check_circle</i>
                        <span>{{ $plan->max_images_per_listing }} images per listing</span>
                    </li>
                    @foreach((array) $plan->features as $feature)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <i class="material-icons text-success text-[15px] shrink-0">check_circle</i>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}"
                   class="mt-6 block w-full rounded-xl px-5 py-3 text-center text-xs font-bold transition-all {{ $isPopular ? 'bg-primary text-white hover:bg-primary-dark shadow-md shadow-primary/20' : 'bg-gray-50 text-dark hover:bg-gray-100' }}">
                    {{ (float) $plan->price_monthly == 0.0 ? 'Get Started Free' : 'Subscribe Now' }}
                </a>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white border border-gray-100 rounded-2xl p-10 text-center">
                <i class="material-icons text-gray-300 text-4xl">sell</i>
                <p class="text-sm text-gray-400 mt-2 mb-0">No subscription plans are available right now.</p>
            </div>
        @endforelse
    </div>

    {{-- Credit Packages --}}
    <div class="mt-16">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-dark">Credit Packages</h2>
            <p class="text-sm text-gray-400 mt-1">Need credits for contact unlocks and promotions? Buy a package.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($creditPackages as $package)
                <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                            <i class="material-icons text-primary text-lg">payments</i>
                        </div>
                        <h3 class="font-bold text-sm text-dark">{{ $package->name }}</h3>
                    </div>
                    <p class="mt-4 text-2xl font-extrabold text-dark mb-0">
                        {{ number_format($package->credits) }} <span class="text-xs font-medium text-gray-400">credits</span>
                    </p>
                    @if($package->bonus_credits > 0)
                        <p class="text-[11px] font-semibold text-success mt-1 mb-0">+ {{ number_format($package->bonus_credits) }} bonus credits</p>
                    @endif
                    <p class="mt-2 text-lg font-bold text-primary mb-0">{{ number_format((float) $package->price) }}</p>
                    <a href="{{ route('register') }}" class="mt-4 block w-full rounded-xl border border-primary text-primary px-4 py-2.5 text-center text-xs font-bold hover:bg-primary hover:text-white transition-all">Buy Now</a>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 bg-white border border-gray-100 rounded-2xl p-10 text-center">
                    <i class="material-icons text-gray-300 text-4xl">account_balance_wallet</i>
                    <p class="text-sm text-gray-400 mt-2 mb-0">No credit packages are available right now.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
