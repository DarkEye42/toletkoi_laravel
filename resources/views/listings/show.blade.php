@extends('layouts.app')

@section('title', $listing->title . ' - ToLetKoi')
@section('description', Str::limit(strip_tags($listing->description), 160))

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": "{{ $listing->title }}",
    "description": "{{ Str::limit(strip_tags($listing->description), 300) }}",
    "image": "{{ $listing->images->count() ? Storage::disk('public')->url($listing->images->first()->path) : '' }}",
    "offers": {
        "@@type": "Offer",
        "price": "{{ $listing->price }}",
        "priceCurrency": "BDT",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8" x-data="{ activeImage: 0, showContact: false }">

    {{-- Breadcrumb --}}
    <nav class="mb-5 text-xs text-gray-400 flex items-center gap-1 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
        <i class="material-icons text-[12px]">chevron_right</i>
        <a href="{{ route('search') }}" class="hover:text-primary transition-colors">Search</a>
        @if($listing->area)
            <i class="material-icons text-[12px]">chevron_right</i>
            <a href="{{ route('area.show', [$listing->area->district->division->slug, $listing->area->district->slug, $listing->area->slug]) }}" class="hover:text-primary transition-colors">{{ $listing->area->name }}</a>
        @endif
        <i class="material-icons text-[12px]">chevron_right</i>
        <span class="text-gray-600 font-medium">{{ Str::limit($listing->title, 40) }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Left Column --}}
        <div class="flex-1 min-w-0">

            {{-- Gallery --}}
            <div class="rounded-2xl overflow-hidden bg-gray-100 border border-gray-100">
                @if($listing->images->count())
                    <div class="relative aspect-[16/10]">
                        @foreach($listing->images as $index => $image)
                            <img
                                x-show="activeImage === {{ $index }}"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                src="{{ Storage::disk('public')->url($image->path) }}"
                                alt="{{ $image->alt_text ?? $listing->title }}"
                                class="absolute inset-0 h-full w-full object-cover"
                            >
                        @endforeach
                        {{-- Image Counter --}}
                        @if($listing->images->count() > 1)
                            <div class="absolute top-4 right-4 bg-dark/60 text-white px-3 py-1.5 rounded-full text-xs font-medium backdrop-blur-sm">
                                <i class="material-icons text-[12px] mr-0.5 align-middle">photo_camera</i>
                                <span x-text="activeImage + 1">1</span> / {{ $listing->images->count() }}
                            </div>
                        @endif
                        {{-- Navigation Arrows --}}
                        @if($listing->images->count() > 1)
                            <button @click="activeImage = activeImage > 0 ? activeImage - 1 : {{ $listing->images->count() - 1 }}"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all backdrop-blur-sm">
                                <i class="material-icons text-dark text-lg">chevron_left</i>
                            </button>
                            <button @click="activeImage = activeImage < {{ $listing->images->count() - 1 }} ? activeImage + 1 : 0"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all backdrop-blur-sm">
                                <i class="material-icons text-dark text-lg">chevron_right</i>
                            </button>
                        @endif
                    </div>
                    {{-- Thumbnails --}}
                    @if($listing->images->count() > 1)
                        <div class="flex gap-2 p-3 overflow-x-auto scrollbar-hide">
                            @foreach($listing->images as $index => $image)
                                <button
                                    @click="activeImage = {{ $index }}"
                                    :class="activeImage === {{ $index }} ? 'ring-2 ring-primary ring-offset-1 opacity-100' : 'opacity-50 hover:opacity-80'"
                                    class="h-16 w-20 shrink-0 overflow-hidden rounded-xl transition-all"
                                >
                                    <img src="{{ Storage::disk('public')->url($image->path) }}" alt="" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
                        <div class="text-center">
                            <i class="material-icons text-primary/20 text-7xl">home</i>
                            <p class="text-xs text-gray-400 mt-2">No photos available</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Title & Price --}}
            <div class="mt-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            @if($listing->is_featured)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200">
                                    <i class="material-icons text-[10px]">star</i> FEATURED
                                </span>
                            @endif
                            @if($listing->listing_type === 'rent')
                                <span class="inline-flex items-center px-2.5 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-bold">FOR RENT</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-secondary/10 text-secondary rounded-full text-[10px] font-bold">FOR SALE</span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-dark leading-tight">{{ $listing->title }}</h1>
                        <div class="flex items-center gap-1.5 mt-2 text-sm text-gray-400">
                            <i class="material-icons text-[14px] text-primary">location_on</i>
                            <span>{{ $listing->address }}{{ $listing->area ? ', ' . $listing->area->name : '' }}{{ $listing->area?->district ? ', ' . $listing->area->district->name : '' }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-2xl md:text-3xl font-extrabold text-primary">৳{{ number_format($listing->price) }}</div>
                        @if($listing->price_type)
                            <div class="text-[11px] text-gray-400 mt-0.5">per {{ str_replace('_', ' ', $listing->price_type) }}</div>
                        @endif
                    </div>
                </div>

                {{-- Badges --}}
                <div class="mt-3 flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/8 text-primary rounded-xl text-[11px] font-semibold">
                        <i class="material-icons text-[12px]">apartment</i> {{ $listing->propertyType->name }}
                    </span>
                    @if($listing->propertySubtype)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-600 rounded-xl text-[11px] font-semibold">
                            <i class="material-icons text-[12px]">category</i> {{ $listing->propertySubtype->name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Property Details Grid --}}
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @if($listing->bedrooms)
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 text-center hover:border-primary/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/8 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="material-icons text-primary text-lg">bed</i>
                        </div>
                        <div class="text-lg font-bold text-dark">{{ $listing->bedrooms }}</div>
                        <div class="text-[10px] text-gray-400">Bedrooms</div>
                    </div>
                @endif
                @if($listing->bathrooms)
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 text-center hover:border-primary/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/8 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="material-icons text-primary text-lg">shower</i>
                        </div>
                        <div class="text-lg font-bold text-dark">{{ $listing->bathrooms }}</div>
                        <div class="text-[10px] text-gray-400">Bathrooms</div>
                    </div>
                @endif
                @if($listing->area_sqft)
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 text-center hover:border-primary/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/8 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="material-icons text-primary text-lg">square_foot</i>
                        </div>
                        <div class="text-lg font-bold text-dark">{{ number_format($listing->area_sqft) }}</div>
                        <div class="text-[10px] text-gray-400">Sq Ft</div>
                    </div>
                @endif
                @if($listing->floor_level)
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 text-center hover:border-primary/30 transition-colors">
                        <div class="w-10 h-10 bg-primary/8 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="material-icons text-primary text-lg">layers</i>
                        </div>
                        <div class="text-sm font-bold text-dark leading-tight">{{ $listing->floor_level }}<span class="text-gray-400 font-normal"> @if($listing->total_floors)/ {{ $listing->total_floors }}@endif</span></div>
                        <div class="text-[10px] text-gray-400">Floor</div>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="mt-8">
                <h2 class="text-lg font-bold text-dark mb-3 flex items-center gap-2">
                    <i class="material-icons text-primary text-xl">description</i> Description
                </h2>
                <div class="text-sm text-gray-500 leading-relaxed bg-gray-50 rounded-2xl p-5 border border-gray-100">
                    {!! nl2br(e($listing->description)) !!}
                </div>
            </div>

            {{-- Features / Amenities --}}
            @if($listing->features)
                @php
                    $featureLabels = [
                        'has_parking' => ['Parking', 'local_parking'],
                        'has_lift' => ['Lift', 'elevator'],
                        'has_generator' => ['Generator', 'bolt'],
                        'has_cctv' => ['CCTV', 'videocam'],
                        'has_security' => ['Security', 'shield'],
                        'has_swimming_pool' => ['Swimming Pool', 'pool'],
                        'has_gym' => ['Gym', 'fitness_center'],
                        'has_rooftop' => ['Rooftop', 'roofing'],
                        'has_balcony' => ['Balcony', 'balcony'],
                        'has_garden' => ['Garden', 'yard'],
                        'has_internet' => ['Internet', 'wifi'],
                        'has_gas' => ['Gas', 'local_fire_department'],
                        'has_water_supply' => ['Water Supply', 'water_drop'],
                        'pet_friendly' => ['Pet Friendly', 'pets'],
                    ];
                    $activeFeatures = collect($featureLabels)->filter(fn($v, $k) => $listing->features->$k ?? false);
                @endphp
                @if($activeFeatures->count())
                <div class="mt-8">
                    <h2 class="text-lg font-bold text-dark mb-3 flex items-center gap-2">
                        <i class="material-icons text-primary text-xl">verified</i> Amenities & Features
                    </h2>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach($activeFeatures as $key => $label)
                            <div class="flex items-center gap-2.5 bg-white border border-gray-100 rounded-xl px-3.5 py-2.5 text-sm text-gray-600 hover:border-primary/30 transition-colors">
                                <i class="material-icons text-primary text-[16px]">{{ $label[1] }}</i>
                                <span class="font-medium text-xs">{{ $label[0] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif

            {{-- Barikoi Map --}}
            @if($listing->lat && $listing->lng)
                <div class="mt-8">
                    <h2 class="text-lg font-bold text-dark mb-3 flex items-center gap-2">
                        <i class="material-icons text-primary text-xl">map</i> Location on Map
                    </h2>
                    <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                        <div id="listing-map" class="h-72 w-full bg-gray-100"
                             data-lat="{{ $listing->lat }}" data-lng="{{ $listing->lng }}"
                             data-title="{{ $listing->title }}"
                             data-address="{{ $listing->address }}">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="material-icons text-[11px]">info</i>
                        Map data powered by Barikoi
                    </p>
                </div>
            @endif

            {{-- Availability --}}
            @if($listing->available_from)
                <div class="mt-6 bg-primary/5 border border-primary/10 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-9 h-9 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                        <i class="material-icons text-primary text-lg">event</i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Available from</p>
                        <p class="text-sm font-bold text-dark">{{ \Carbon\Carbon::parse($listing->available_from)->format('F j, Y') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Contact & Sidebar --}}
        <aside class="w-full lg:w-[340px] shrink-0 space-y-5">

            {{-- Contact Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm sticky top-24">
                @if($contactUnlocked)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="material-icons text-primary text-lg">person</i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-dark">Contact Information</h3>
                            <p class="text-[10px] text-gray-400">Property owner details</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="material-icons text-gray-400 text-lg">badge</i>
                            <span class="font-medium text-sm text-dark">{{ $listing->contact_name ?? 'Property Owner' }}</span>
                        </div>
                        @if($listing->contact_phone)
                            <a href="tel:{{ $listing->contact_phone }}" class="flex items-center gap-3 p-3 bg-primary/5 border border-primary/10 rounded-xl hover:bg-primary/10 transition-colors">
                                <i class="material-icons text-primary text-lg">phone</i>
                                <span class="font-semibold text-sm text-primary">{{ $listing->contact_phone }}</span>
                            </a>
                        @endif
                        @if($listing->contact_email)
                            <a href="mailto:{{ $listing->contact_email }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <i class="material-icons text-gray-400 text-lg">email</i>
                                <span class="text-sm text-primary font-medium">{{ $listing->contact_email }}</span>
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Contact Locked --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                            <i class="material-icons text-gray-400 text-lg">lock</i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-dark">Contact Owner</h3>
                            <p class="text-[10px] text-gray-400">Unlock to view details</p>
                        </div>
                    </div>

                    @auth
                        <button
                            @click="
                                fetch(`/api/listings/{{ $listing->id }}/unlock-contact`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(r => r.json())
                                .then(d => { if(d.success) location.reload(); else alert(d.message || 'Failed to unlock.'); })
                                .catch(() => alert('Error. Please try again.'))
                            "
                            class="w-full bg-primary hover:bg-primary-dark text-white px-5 py-3 rounded-xl text-sm font-bold shadow-md shadow-primary/20 flex items-center justify-center gap-2 transition-all hover:shadow-lg hover:shadow-primary/30"
                        >
                            <i class="material-icons text-[16px]">lock_open</i>
                            Unlock Contact (50 credits)
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-primary hover:bg-primary-dark text-white px-5 py-3 rounded-xl text-sm font-bold text-center shadow-md shadow-primary/20 transition-all">
                            Login to Unlock
                        </a>
                    @endauth
                @endif

                {{-- Quick Actions --}}
                <div class="mt-4 space-y-2">
                    @auth
                        <form action="{{ route('favorites.toggle', $listing) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full border border-gray-200 hover:border-primary/30 hover:bg-primary/5 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                                <i class="material-icons text-[16px]">favorite_border</i> Save to Favorites
                            </button>
                        </form>
                    @endauth
                    <button class="w-full border border-gray-200 hover:border-primary/30 hover:bg-primary/5 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all"
                            onclick="navigator.share?.({ title: '{{ $listing->title }}', url: window.location.href })">
                        <i class="material-icons text-[16px]">share</i> Share Listing
                    </button>
                </div>

                {{-- Trust Info --}}
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="material-icons text-[13px] text-gray-400">visibility</i>
                        {{ number_format($listing->stats->view_count ?? 0) }} views
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="material-icons text-[13px] text-gray-400">schedule</i>
                        Listed {{ $listing->created_at->diffForHumans() }}
                    </div>
                    @if($listing->user?->profile && $listing->user->isVerified())
                        <div class="flex items-center gap-2 text-xs text-primary font-medium">
                            <i class="material-icons text-[13px]">verified</i>
                            Verified Owner
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    {{-- Related Listings --}}
    @if($relatedListings->count())
        <div class="mt-12">
            <h2 class="text-xl font-bold text-dark mb-6 flex items-center gap-2">
                <i class="material-icons text-primary text-2xl">home_work</i> Similar Properties
            </h2>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedListings as $related)
                    <x-listings.card :listing="$related" />
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapEl = document.getElementById('listing-map');
        if (!mapEl) return;

        // The Leaflet bundle is loaded from a CDN. If that request fails
        // (offline, CSP, proxy) `L` is undefined and the old code threw a
        // ReferenceError, leaving the map area blank with no explanation.
        // Wait for the library briefly, then bail out gracefully.
        waitForLeaflet(function() { initListingMap(mapEl); });
    });

    /**
     * Run `callback` as soon as window.L exists, polling for up to ~5s.
     * window.L is checked on every tick because a deferred/externally
     * injected Leaflet script can appear at any moment.
     */
    function waitForLeaflet(callback) {
        let attempts = 0;
        const timer = setInterval(function() {
            if (window.L) {
                clearInterval(timer);
                callback();
            } else if (++attempts >= 25) {
                clearInterval(timer);
                const el = document.getElementById('listing-map');
                if (el) {
                    el.innerHTML = '<div style="padding:24px;text-align:center;color:#6b7280;font-size:12px;">Map unavailable - the map library could not be loaded.</div>';
                }
            }
        }, 200);
    }

    function initListingMap(mapEl) {
        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        const title = mapEl.dataset.title;
        const address = mapEl.dataset.address;

        if (isNaN(lat) || isNaN(lng)) return;

        // Initialize Leaflet map
        const map = L.map('listing-map', {
            scrollWheelZoom: false,
        }).setView([lat, lng], 16);

        // OpenStreetMap fallback tiles
        const OSM_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const OSM_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
        const BARIKOI_ATTRIBUTION = '&copy; <a href="https://barikoi.com">Barikoi</a>';

        // Use Barikoi tiles if available, otherwise fall back to OSM
        function loadMapTiles() {
            const config = window.BARIKOI_CONFIG;
            if (config && config.tile_url) {
                L.tileLayer(config.tile_url, { maxZoom: 19, attribution: BARIKOI_ATTRIBUTION }).addTo(map);
            } else {
                L.tileLayer(OSM_URL, { maxZoom: 19, attribution: OSM_ATTRIBUTION }).addTo(map);
            }
        }

        loadMapTiles();

        // Custom primary marker
        const primaryIcon = L.divIcon({
            className: 'custom-barikoi-marker',
            html: '<div style="width:36px;height:36px;background:#00a1ff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><i class="material-icons" style="transform:rotate(45deg);color:#fff;font-size:16px;">home</i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36],
        });

        L.marker([lat, lng], { icon: primaryIcon })
            .addTo(map)
            .bindPopup('<div style="min-width:150px;"><strong style="font-size:13px;">' + title + '</strong><br><span style="font-size:11px;color:#666;">' + address + '</span></div>')
            .openPopup();

        // Fix map rendering after load
        setTimeout(() => map.invalidateSize(), 200);
    }
</script>
<style>
    .custom-barikoi-marker {
        background: none !important;
        border: none !important;
    }
</style>
@endpush
@endsection
