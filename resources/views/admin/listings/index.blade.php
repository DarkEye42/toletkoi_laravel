@extends('layouts.admin')

@section('title', 'Listings')
@section('header', 'Listing Moderation')

@section('content')
    <div class="space-y-6">

        {{-- Status tabs --}}
        <div class="card">
            <div class="card-body p-2">
                <div class="flex flex-wrap gap-1">
                    @foreach ([['key' => '', 'label' => 'All', 'count' => $counts['all']], ['key' => 'pending', 'label' => 'Pending', 'count' => $counts['pending']], ['key' => 'active', 'label' => 'Active', 'count' => $counts['active']], ['key' => 'rejected', 'label' => 'Rejected', 'count' => $counts['rejected']]] as $tab)
                        @php $isActive = request('status', '') === $tab['key']; @endphp
                        <a href="{{ route('admin.listings.index', array_filter(['status' => $tab['key'], 'q' => request('q'), 'property_type_id' => request('property_type_id')])) }}"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors
                           {{ $isActive ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-primary' }}">
                            {{ $tab['label'] }}
                            <span
                                class="text-[10px] px-1.5 py-0.5 rounded-full {{ $isActive ? 'bg-white/25' : 'bg-gray-100 text-gray-500' }}">
                                {{ $tab['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.listings.index') }}" class="card">
            <div class="card-body p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <input type="hidden" name="status" value="{{ request('status') }}">

                    <div class="relative lg:col-span-2">
                        <i
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[18px] pointer-events-none">search</i>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search title or address..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-xs font-medium text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary placeholder:text-gray-400">
                    </div>

                    <select name="property_type_id"
                        class="w-full px-3 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-xs font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                        <option value="">All property types</option>
                        @foreach ($propertyTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ request('property_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <select name="sort"
                            class="flex-1 px-3 py-2.5 bg-gray-50 border-gray-200 rounded-xl text-xs font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                            <option value="newest" {{ request('sort') !== 'oldest' ? 'selected' : '' }}>Newest first
                            </option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest first
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm shrink-0">Filter</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Queue --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h5 class="text-base font-bold text-dark mb-0">
                    {{ $listings->total() }} listing{{ $listings->total() == 1 ? '' : 's' }}
                </h5>
                @if (request()->hasAny(['q', 'status', 'property_type_id', 'sort']))
                    <a href="{{ route('admin.listings.index') }}"
                        class="text-xs font-semibold text-primary hover:text-primary-dark transition-colors">Clear
                        filters</a>
                @endif
            </div>

            @if ($listings->count())
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Listing</th>
                                <th>Owner</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listings as $listing)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if ($listing->images->count())
                                                <img src="{{ $listing->images->first()->getThumbnailUrl() }}"
                                                    alt="{{ $listing->title }}"
                                                    class="h-11 w-14 rounded-lg object-cover shrink-0" loading="lazy">
                                            @else
                                                <div
                                                    class="h-11 w-14 rounded-lg bg-primary/5 flex items-center justify-center shrink-0">
                                                    <i class="material-icons text-primary/40 text-[18px]">apartment</i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.listings.show', $listing) }}"
                                                    class="font-semibold text-dark text-xs hover:text-primary transition-colors line-clamp-1">
                                                    {{ Str::limit($listing->title, 38) }}
                                                </a>
                                                <div class="text-[10px] text-muted">
                                                    {{ $listing->propertyType->name ?? '—' }}
                                                    @if ($listing->is_featured)
                                                        · <span class="text-warning font-bold">Featured</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-xs text-muted">{{ Str::limit($listing->user->name ?? '—', 18) }}</td>
                                    <td class="text-xs text-muted">
                                        {{ $listing->area->name ?? '—' }}@if ($listing->district)
                                            , {{ $listing->district->name }}
                                        @endif
                                    </td>
                                    <td class="text-xs font-bold text-dark">৳{{ number_format($listing->price) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $listing->status === 'active' ? 'badge-success' : ($listing->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                            {{ ucfirst($listing->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($listing->status !== 'active')
                                                <form method="POST"
                                                    action="{{ route('admin.listings.approve', $listing) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        title="Approve and publish">
                                                        <i class="material-icons text-[14px]">check</i> Approve
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($listing->status === 'pending')
                                                <button type="button"
                                                    @click="$dispatch('open-reject', { id: {{ $listing->id }}, title: @js($listing->title) })"
                                                    class="btn btn-danger btn-sm" title="Reject">
                                                    <i class="material-icons text-[14px]">close</i>
                                                </button>
                                            @endif

                                            <form method="POST" action="{{ route('admin.listings.feature', $listing) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $listing->is_featured ? 'btn-warning' : 'btn-light' }}"
                                                    title="{{ $listing->is_featured ? 'Remove from featured' : 'Feature for 30 days' }}">
                                                    <i class="material-icons text-[14px]">star</i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.listings.show', $listing) }}"
                                                class="btn btn-light btn-sm" title="View details">
                                                <i class="material-icons text-[14px]">visibility</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $listings->links() }}
                </div>
            @else
                <div class="card-body p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="material-icons text-gray-300 text-3xl">inbox</i>
                    </div>
                    <h3 class="text-lg font-bold text-dark">No listings match</h3>
                    <p class="text-sm text-muted mt-1.5 mb-0">Try clearing the filters to see the full queue.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Reject modal --}}
    <div x-data="{ open: false, id: null, title: '' }" @open-reject.window="open = true; id = $event.detail.id; title = $event.detail.title"
        x-cloak>
        <div x-show="open" class="fixed inset-0 z-100 bg-dark/50 flex items-start justify-center pt-[10vh] px-4"
            x-transition.opacity>
            <div class="absolute inset-0" @click="open = false"></div>
            <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl" x-transition>
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-base font-bold text-dark mb-0">Reject listing</h2>
                    <p class="text-xs text-muted mt-1 mb-0" x-text="title"></p>
                </div>
                <form method="POST" :action="'{{ url('admin/listings') }}/' + id + '/reject'">
                    @csrf
                    <div class="px-6 py-5">
                        <label class="label">Reason (visible to the owner)</label>
                        <textarea name="rejection_reason" rows="4" required maxlength="500" class="input"
                            placeholder="e.g. Photos do not match the described property."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="open = false"
                            class="text-sm font-medium text-gray-500 hover:text-dark transition-colors">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="material-icons text-[14px]">close</i> Reject listing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
