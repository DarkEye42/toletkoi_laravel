@extends('layouts.dashboard')
@section('title', 'Saved Searches')
@section('header', 'Saved Searches')

@section('content')
@if($savedSearches->count())
    <div class="space-y-4">
        @foreach($savedSearches as $search)
            <div class="card">
                <div class="card-body p-4 flex-col sm:flex-row sm:items-center gap-4 sm:justify-between">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <i class="material-icons text-primary text-lg">saved_search</i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm text-dark mb-0">{{ $search->name }}</h4>
                            <p class="text-[11px] text-muted mb-0 truncate">{{ json_encode($search->filters) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('search') . '?' . http_build_query($search->filters ?? []) }}" class="btn btn-primary btn-sm">
                            <i class="material-icons text-[14px]">search</i> Run Search
                        </a>
                        <form action="{{ route('dashboard.saved-searches.destroy', $search) }}" method="POST" onsubmit="return confirm('Delete this saved search?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 rounded-lg flex items-center justify-center text-danger hover:bg-danger-light transition-colors" title="Delete">
                                <i class="material-icons text-[18px]">delete_outline</i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $savedSearches->links() }}</div>
@else
    <div class="card">
        <div class="card-body p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="material-icons text-gray-300 text-3xl">saved_search</i>
            </div>
            <h3 class="text-lg font-bold text-dark">No saved searches</h3>
            <p class="text-sm text-muted mt-1.5 max-w-sm mx-auto">Save your search filters for quick access later.</p>
            <a href="{{ route('search') }}" class="mt-5 inline-flex btn-primary">
                <i class="material-icons text-[16px]">search</i> Start Searching
            </a>
        </div>
    </div>
@endif
@endsection
