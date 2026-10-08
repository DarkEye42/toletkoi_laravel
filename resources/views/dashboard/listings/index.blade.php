@extends('layouts.dashboard')

@section('title', 'My Listings')
@section('header', 'My Listings')

@section('header-actions')
<a href="{{ route('dashboard.listings.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark transition">
     New Listing
</a>
@endsection

@section('content')
@if($listings->count())
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($listings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($listing->images->count())
                                    <img src="{{ Storage::disk('public')->url($listing->images->first()->path) }}" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xl"></div>
                                @endif
                                <div>
                                    <p class="font-medium text-dark">{{ Str::limit($listing->title, 35) }}</p>
                                    <p class="text-xs text-muted">{{ $listing->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">{{ $listing->propertyType->name ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-dark">৳{{ number_format($listing->price) }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">{{ number_format($listing->views_count) }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                {{ $listing->status === 'active' ? 'bg-green-50 text-green-700' : '' }}
                                {{ $listing->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                {{ $listing->status === 'rejected' ? 'bg-red-50 text-red-700' : '' }}
                                {{ $listing->status === 'inactive' ? 'bg-gray-50 text-gray-700' : '' }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                @if($listing->status === 'active')
                                    <a href="{{ $listing->getUrl() }}" target="_blank" class="text-muted hover:text-gray-700" title="View">👁️</a>
                                @endif
                                <a href="{{ route('dashboard.listings.edit', $listing) }}" class="text-primary hover:text-primary" title="Edit">✏️</a>
                                <form action="{{ route('dashboard.listings.duplicate', $listing) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-700" title="Duplicate"></button>
                                </form>
                                <form action="{{ route('dashboard.listings.destroy', $listing) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this listing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Delete"></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $listings->links() }}
    </div>
@else
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm border border-gray-100">
        <div class="text-4xl"></div>
        <h3 class="mt-4 text-lg font-semibold text-dark">No listings yet</h3>
        <p class="mt-2 text-muted">Start by creating your first property listing.</p>
        <a href="{{ route('dashboard.listings.create') }}" class="mt-4 inline-flex rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-primary-dark transition">
             Create Listing
        </a>
    </div>
@endif
@endsection
