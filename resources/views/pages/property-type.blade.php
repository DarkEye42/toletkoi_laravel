@extends('layouts.app')

@section('title', $propertyType->name . ' Properties - ToLetKoi')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">{{ $propertyType->name }} Properties</h1>
        @if($propertyType->description)
            <p class="mt-3 text-gray-600">{{ $propertyType->description }}</p>
        @endif
    </div>

    @if($listings->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($listings as $listing)
                @include('components.listings.card', ['listing' => $listing])
            @endforeach
        </div>
        <div class="mt-8">{{ $listings->links() }}</div>
    @else
        <p class="text-gray-500 text-center py-10">No {{ strtolower($propertyType->name) }} properties listed yet.</p>
    @endif
</div>
@endsection
