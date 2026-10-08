@extends('layouts.app')

@section('title', $area->name . ' - ToLetKoi')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <p class="text-sm text-emerald-600">
            <a href="{{ route('district.show', ['division' => $area->district->division->slug ?? $area->district->division->id, 'district' => $area->district->slug ?? $area->district->id]) }}" class="hover:underline">{{ $area->district->name ?? 'District' }}</a>
        </p>
        <h1 class="text-4xl font-bold text-gray-900">{{ $area->name }}</h1>
        <p class="mt-3 text-gray-600">Browse properties in {{ $area->name }}</p>
    </div>

    @if($listings->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($listings as $listing)
                @include('components.listings.card', ['listing' => $listing])
            @endforeach
        </div>
        <div class="mt-8">{{ $listings->links() }}</div>
    @else
        <p class="text-gray-500 text-center py-10">No properties listed in this area yet.</p>
    @endif
</div>
@endsection
