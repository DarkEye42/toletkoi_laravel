@extends('layouts.dashboard')
@section('title', 'Favorites')
@section('header', 'My Favorites')

@section('content')
@if($favorites->count())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($favorites as $fav)
            @if($fav->listing)
            <div class="relative">
                <x-listings.card :listing="$fav->listing" />
                <form action="{{ route('dashboard.favorites.destroy', $fav->listing) }}" method="POST" class="absolute top-3 right-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full bg-red-500 p-1.5 text-white shadow hover:bg-red-600 transition" title="Remove">✕</button>
                </form>
            </div>
            @endif
        @endforeach
    </div>
    <div class="mt-6">{{ $favorites->links() }}</div>
@else
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="material-icons text-gray-300 text-3xl">favorite_border</i></div>
        <h3 class="mt-4 text-lg font-semibold text-dark">No favorites yet</h3>
        <p class="mt-2 text-muted">Start browsing and save properties you love.</p>
        <a href="{{ route('search') }}" class="mt-4 inline-flex rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-primary-dark transition"><i class="material-icons text-[16px]">search</i> Browse Properties</a>
    </div>
@endif
@endsection
