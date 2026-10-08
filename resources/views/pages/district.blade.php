@extends('layouts.app')

@section('title', $district->name . ' - ToLetKoi')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <p class="text-sm text-emerald-600"><a href="{{ route('division.show', ['division' => $district->division->slug ?? $district->division->id]) }}" class="hover:underline">{{ $district->division->name ?? 'Division' }}</a></p>
        <h1 class="text-4xl font-bold text-gray-900">{{ $district->name }}</h1>
        <p class="mt-3 text-gray-600">Browse properties in {{ $district->name }} district</p>
    </div>

    @if($areas->count())
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Areas in {{ $district->name }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($areas as $area)
                    <a href="{{ route('area.show', ['division' => $district->division->slug ?? $district->division->id, 'district' => $district->slug ?? $district->id, 'area' => $area->slug ?? $area->id]) }}"
                       class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 hover:ring-emerald-300 transition">
                        <span class="font-medium text-gray-900">{{ $area->name }}</span>
                        <span class="text-emerald-600">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($listings->count())
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Properties in {{ $district->name }}</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($listings as $listing)
                    @include('components.listings.card', ['listing' => $listing])
                @endforeach
            </div>
            <div class="mt-8">{{ $listings->links() }}</div>
        </div>
    @else
        <p class="text-gray-500 text-center py-10">No properties listed in this district yet.</p>
    @endif
</div>
@endsection
