@extends('layouts.dashboard')
@section('title', 'KoiFind Details')
@section('header', 'KoiFind: ' . Str::limit($koifind->title, 40))

@section('content')
<div class="max-w-3xl space-y-8">
    {{-- Request Details --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-dark">Request Details</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 text-sm">
            <div><span class="text-muted">Type:</span> <span class="font-medium text-dark">{{ ucfirst($koifind->listing_type) }}</span></div>
            <div><span class="text-muted">Status:</span> <span class="font-medium text-dark">{{ ucfirst($koifind->status) }}</span></div>
            <div><span class="text-muted">Budget:</span> <span class="font-medium text-dark">{{ $koifind->budget_min ? '৳'.number_format($koifind->budget_min) : 'Any' }} – {{ $koifind->budget_max ? '৳'.number_format($koifind->budget_max) : 'Any' }}</span></div>
            <div><span class="text-muted">Bedrooms:</span> <span class="font-medium text-dark">{{ $koifind->bedrooms ?? 'Any' }}</span></div>
            @if($koifind->preferred_areas)
                <div class="sm:col-span-2"><span class="text-muted">Preferred Areas:</span> <span class="font-medium text-dark">{{ $koifind->preferred_areas }}</span></div>
            @endif
            @if($koifind->description)
                <div class="sm:col-span-2"><span class="text-muted">Requirements:</span> <span class="font-medium text-dark">{{ $koifind->description }}</span></div>
            @endif
        </div>
    </div>

    {{-- Candidates --}}
    <div>
        <h3 class="text-lg font-semibold text-dark mb-4">Agent Candidates ({{ $koifind->candidates->count() }})</h3>
        @if($koifind->candidates->count())
            <div class="space-y-4">
                @foreach($koifind->candidates as $candidate)
                    <div class="rounded-2xl bg-white p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-dark">{{ $candidate->serviceProvider?->user?->name ?? 'Agent' }}</p>
                                <p class="text-sm text-muted">{{ $candidate->notes ?? 'No notes' }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium bg-gray-50 text-gray-700">{{ ucfirst($candidate->status) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted text-sm">No candidates yet. Agents will be notified about your request.</p>
        @endif
    </div>
</div>
@endsection
