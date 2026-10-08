@extends('layouts.admin')

@section('title', 'Listing Detail')
@section('header', 'Listing Review')

@section('header-actions')
    <a href="{{ route('admin.listings.index') }}" class="btn btn-light btn-sm">
        <i class="material-icons text-[14px]">arrow_back</i> Back to queue
    </a>
@endsection

@section('content')
    @php
        $images = $listing->images->sortBy('sort_order')->values();
    @endphp
    <div class="space-y-6">

        {{-- Status + actions --}}
        <div class="card">
            <div class="card-body p-6 flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                    {{ $listing->status === 'active' ? 'bg-success-light text-success' : ($listing->status === 'pending' ? 'bg-warning-light text-warning' : 'bg-danger-light text-danger') }}">
                        <i class="material-icons">
                            {{ $listing->status === 'active' ? 'check_circle' : ($listing->status === 'pending' ? 'hourglass_empty' : 'cancel') }}
                        </i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-0">Current status</p>
                        <p class="text-base font-bold text-dark mb-0">{{ ucfirst($listing->status) }}</p>
                        @if ($listing->status === 'rejected' && $listing->rejection_reason)
                            <p class="text-[11px] text-danger mb-0">Reason: {{ $listing->rejection_reason }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($listing->status !== 'active')
                        <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"><i
                                    class="material-icons text-[14px]">check</i> Approve</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.listings.feature', $listing) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $listing->is_featured ? 'btn-warning' : 'btn-light' }}">
                            <i class="material-icons text-[14px]">star</i>
                            {{ $listing->is_featured ? 'Unfeature' : 'Feature 30 days' }}
                        </button>
                    </form>

                    @if ($listing->status === 'active')
                        <a href="{{ $listing->getUrl() }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">
                            <i class="material-icons text-[14px]">open_in_new</i> View public page
                        </a>
                    @endif

                    <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}"
                        onsubmit="return confirm('Permanently delete this listing and its images? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i
                                class="material-icons text-[14px]">delete_outline</i> Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Rejection form --}}
        @if ($listing->status === 'pending')
            <div class="card border-warning/30">
                <div class="card-header">
                    <h5 class="text-base font-bold text-dark mb-0">Reject this listing</h5>
                    <p class="text-xs text-muted mb-0">The reason is shown to the owner so they can correct the submission.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.listings.reject', $listing) }}">
                    @csrf
                    <div class="card-body">
                        <textarea name="rejection_reason" rows="3" required maxlength="500" class="input"
                            placeholder="Explain why this listing is being rejected..."></textarea>
                    </div>
                    <div class="card-footer flex justify-end">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="material-icons text-[14px]">close</i>
                            Reject listing</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main detail --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Images --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-base font-bold text-dark mb-0">Photos ({{ $images->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if ($images->count())
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($images as $image)
                                    <div class="relative rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ $image->getUrl() }}" alt="{{ $listing->title }}"
                                            class="w-full h-32 object-cover" loading="lazy">
                                        @if ($image->is_primary)
                                            <span class="absolute top-2 left-2 badge-primary">Primary</span>
                                        @endif
                                        @if ($image->getReadableSize())
                                            <span
                                                class="absolute bottom-2 right-2 bg-dark/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                                {{ $image->getReadableSize() }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="material-icons text-gray-300 text-3xl">hide_image</i>
                                <p class="text-xs text-muted mt-2 mb-0">This listing has no photos.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-base font-bold text-dark mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-sm text-gray-600 leading-relaxed">
                            {!! nl2br(e($listing->description)) !!}
                        </div>
                        @if ($listing->description_bn)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-[11px] font-bold text-muted uppercase tracking-wide mb-2">Bengali description
                                </p>
                                <div class="text-sm text-gray-600 leading-relaxed">{!! nl2br(e($listing->description_bn)) !!}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Features --}}
                @if ($listing->features)
                    @php
                        $featureLabels = [
                            'has_parking' => 'Parking',
                            'has_lift' => 'Lift',
                            'has_generator' => 'Generator',
                            'has_gas' => 'Gas',
                            'electricity_included' => 'Electricity included',
                            'water_included' => 'Water included',
                            'has_security' => 'Security',
                            'has_cctv' => 'CCTV',
                            'has_wifi' => 'WiFi',
                            'is_furnished' => 'Furnished',
                            'has_ac' => 'AC',
                            'has_washing_machine' => 'Washing machine',
                            'shared_kitchen' => 'Shared kitchen',
                            'attached_bathroom' => 'Attached bathroom',
                        ];
                        $present = collect($featureLabels)->filter(fn($label, $col) => $listing->features->{$col});
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <h5 class="text-base font-bold text-dark mb-0">Amenities</h5>
                        </div>
                        <div class="card-body">
                            @if ($present->count())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($present as $label)
                                        <span class="badge badge-success">{{ $label }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-muted mb-0">No amenities recorded.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-base font-bold text-dark mb-0">Property</h5>
                    </div>
                    <div class="card-body">
                        <dl class="space-y-3 text-xs">
                            @php
                                $rows = [
                                    'Title' => $listing->title,
                                    'Type' => $listing->propertyType->name ?? '—',
                                    'Subtype' => $listing->propertySubtype->name ?? '—',
                                    'Listing type' => ucfirst($listing->listing_type ?? '—'),
                                    'Price' => '৳' . number_format($listing->price),
                                    'Bedrooms' => $listing->bedrooms,
                                    'Bathrooms' => $listing->bathrooms,
                                    'Balconies' => $listing->balconies,
                                    'Area' => $listing->area_sqft ? number_format($listing->area_sqft) . ' sqft' : '—',
                                    'Furnishing' => ucfirst(str_replace('_', ' ', $listing->furnishing_status ?? '—')),
                                ];
                            @endphp
                            @foreach ($rows as $label => $value)
                                <div
                                    class="flex justify-between gap-3 pb-2.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                    <dt class="text-muted font-medium shrink-0">{{ $label }}</dt>
                                    <dd class="text-dark font-semibold text-right mb-0">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="text-base font-bold text-dark mb-0">Location</h5>
                    </div>
                    <div class="card-body">
                        <dl class="space-y-3 text-xs">
                            @foreach ([
            'Division' => $listing->division->name ?? '—',
            'District' => $listing->district->name ?? '—',
            'Area' => $listing->area->name ?? '—',
            'Address' => $listing->address,
            'Coordinates' => $listing->lat && $listing->lng ? $listing->lat . ', ' . $listing->lng : 'Not set',
        ] as $label => $value)
                                <div
                                    class="flex justify-between gap-3 pb-2.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                    <dt class="text-muted font-medium shrink-0">{{ $label }}</dt>
                                    <dd class="text-dark font-semibold text-right mb-0">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="text-base font-bold text-dark mb-0">Owner</h5>
                    </div>
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm uppercase shrink-0">
                                {{ substr($listing->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-dark truncate mb-0">
                                    {{ $listing->user->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-muted truncate mb-0">{{ $listing->user->email ?? '—' }}</p>
                            </div>
                        </div>
                        <dl class="space-y-2.5 text-xs">
                            <div class="flex justify-between gap-3">
                                <dt class="text-muted font-medium">Contact name</dt>
                                <dd class="text-dark font-semibold mb-0">{{ $listing->contact_name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-muted font-medium">Contact phone</dt>
                                <dd class="text-dark font-semibold mb-0">{{ $listing->contact_phone ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-muted font-medium">Views</dt>
                                <dd class="text-dark font-semibold mb-0">
                                    {{ number_format($listing->stats->view_count ?? 0) }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-muted font-medium">Submitted</dt>
                                <dd class="text-dark font-semibold mb-0">{{ $listing->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
