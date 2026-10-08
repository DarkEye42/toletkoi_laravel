@extends('layouts.dashboard')
@section('title', 'KoiFind')
@section('header', 'KoiFind')

@section('content')
<div class="space-y-8">
    {{-- Create New Request --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-dark">Create KoiFind Request</h3>
        <p class="mt-1 text-sm text-muted">Tell us what you're looking for and agents will contact you with matching properties.</p>
        <form action="{{ route('dashboard.koifind.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. 2BR apartment in Dhanmondi under 20k" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Property Type</label>
                    <select name="property_type_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                        <option value="">Any</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="listing_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                        <option value="rent">Rent</option>
                        <option value="sale">Sale</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Budget (৳)</label>
                    <input type="number" name="budget_min" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Budget (৳)</label>
                    <input type="number" name="budget_max" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bedrooms</label>
                    <input type="number" name="bedrooms" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Preferred Areas</label>
                    <input type="text" name="preferred_areas" placeholder="e.g. Dhanmondi, Mirpur, Uttara" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Additional Requirements</label>
                    <textarea name="description" rows="3" placeholder="Any specific requirements..." class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></textarea>
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-primary-dark transition">Submit Request</button>
        </form>
    </div>

    {{-- Previous Requests --}}
    @if($requests->count())
        <div>
            <h3 class="text-lg font-semibold text-dark mb-4">Previous Requests</h3>
            <div class="space-y-4">
                @foreach($requests as $req)
                    <a href="{{ route('dashboard.koifind.show', $req) }}" class="block rounded-2xl bg-white p-4 shadow-sm border border-gray-100 hover:border-primary/30 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-dark">{{ $req->title }}</h4>
                                <p class="text-sm text-muted mt-1">
                                    {{ ucfirst($req->listing_type) }} · {{ $req->budget_min ? '৳'.number_format($req->budget_min) : 'Any' }} – {{ $req->budget_max ? '৳'.number_format($req->budget_max) : 'Any' }}
                                    · {{ $req->candidates_count ?? $req->candidates->count() }} candidates
                                </p>
                            </div>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $req->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700' }}">{{ ucfirst($req->status) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
