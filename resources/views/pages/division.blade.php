@extends('layouts.app')

@section('title', $division->name . ' - ToLetKoi')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">{{ $division->name }}</h1>
        <p class="mt-3 text-gray-600">Browse properties in {{ $division->name }} division</p>
    </div>

    @if($districts->count())
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Districts in {{ $division->name }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($districts as $district)
                    <a href="{{ route('district.show', ['division' => $division->slug ?? $division->id, 'district' => $district->slug ?? $district->id]) }}"
                       class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 hover:ring-emerald-300 transition">
                        <span class="font-medium text-gray-900">{{ $district->name }}</span>
                        <span class="text-emerald-600">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($listings->count())
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Properties in {{ $division->name }}</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($listings as $listing)
                    @include('components.listings.card', ['listing' => $listing])
                @endforeach
            </div>
            <div class="mt-8">{{ $listings->links() }}</div>
        </div>
    @else
        <p class="text-gray-500 text-center py-10">No properties listed in this division yet.</p>
    @endif
</div>
@endsection
