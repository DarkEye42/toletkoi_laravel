@extends('layouts.app')

@section('title', 'All Properties - ToLetKoi')
@section('description', 'Browse all property listings on ToLetKoi - Bangladesh\'s trusted rental marketplace.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">All Properties</h1>
        <p class="mt-3 text-gray-600">{{ $listings->total() }} properties available</p>
    </div>

    @if($listings->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($listings as $listing)
                @include('components.listings.card', ['listing' => $listing])
            @endforeach
        </div>
        <div class="mt-10">{{ $listings->links() }}</div>
    @else
        <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-200">
            <div class="text-5xl">🏠</div>
            <h3 class="mt-4 text-xl font-bold text-gray-900">No properties found</h3>
            <p class="mt-2 text-gray-500">Check back later or try searching with different filters.</p>
            <a href="{{ route('search') }}" class="mt-6 inline-flex rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700 transition">🔍 Search Properties</a>
        </div>
    @endif
</div>
@endsection
