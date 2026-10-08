@extends('layouts.dashboard')
@section('title', 'Credit History')
@section('header', 'Credit Transaction History')

@section('content')
@if($transactions->count())
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-muted">Balance After</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transactions as $txn)
                    <tr>
                        <td class="px-6 py-4 text-sm text-muted">{{ $txn->created_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $txn->type === 'credit' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ ucfirst($txn->type) }}
                            </span>
                        </td>
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
    <div class="mt-6">{{ $transactions->links() }}</div>
@else
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm border border-gray-100">
        <p class="text-muted">No transactions yet.</p>
    </div>
@endif
@endsection
