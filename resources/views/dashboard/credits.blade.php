@extends('layouts.dashboard')
@section('title', 'Credit Wallet')
@section('header', 'Credit Wallet ')

@section('content')
<div class="space-y-8">
    {{-- Balance Card --}}
    <div class="rounded-xl bg-gradient-to-r from-primary to-secondary p-8 text-white">
        <p class="text-emerald-100">Available Balance</p>
        <p class="mt-2 text-4xl font-bold">{{ number_format($wallet->balance) }} credits</p>
        <p class="mt-1 text-sm text-emerald-200">1 credit ≈ ৳1</p>
    </div>

    {{-- Purchase Credits --}}
    <div>
        <h3 class="text-lg font-semibold text-dark mb-4">Purchase Credits</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($packages as $package)
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:border-primary/30 transition">
                    <h4 class="font-semibold text-dark">{{ $package->name }}</h4>
                    <p class="mt-2 text-2xl font-bold text-primary">{{ number_format($package->credits) }} credits</p>
                    @if($package->bonus_credits > 0)
                        <p class="text-xs text-success">+{{ $package->bonus_credits }} bonus credits!</p>
                    @endif
                    <p class="mt-2 text-lg font-semibold text-dark">৳{{ number_format($package->price) }}</p>
                    <form action="{{ route('dashboard.credits.purchase', $package) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-dark transition">
                            Buy Now
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-dark">Recent Transactions</h3>
            <a href="{{ route('dashboard.credits.history') }}" class="text-sm text-primary hover:text-primary">View All →</a>
        </div>
        @if($recentTransactions->count())
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentTransactions as $txn)
                            <tr>
                                <td class="px-6 py-4 text-sm text-muted">{{ $txn->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-dark">{{ $txn->description }}</td>
                                <td class="px-6 py-4 text-sm font-medium {{ $txn->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $txn->type === 'credit' ? '+' : '-' }}{{ number_format($txn->amount) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-muted">{{ number_format($txn->balance_after) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-sm">No transactions yet.</p>
        @endif
    </div>
</div>
@endsection
