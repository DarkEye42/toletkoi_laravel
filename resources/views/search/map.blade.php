@extends('layouts.app')

@section('title', 'Map Search - ToLetKoi')
@section('description', 'Search properties on an interactive map of Bangladesh powered by Barikoi.')

@section('content')
<div x-data="mapSearch()" x-init="init()" class="h-[calc(100vh-140px)] flex flex-col gap-3 mx-4">

    {{-- ==================== COMPACT FILTER BAR ==================== --}}
    <div class="flex-shrink-0 bg-white border border-gray-200 mt-3 rounded-2xl shadow-sm px-4 py-2.5">
        <form id="mapFilterForm" class="max-w-6xl mx-auto px-4 flex items-center gap-2 flex-wrap">
            {{-- Type --}}
            <select name="listing_type" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-2 text-[11px] font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Types</option>
                <option value="rent">Rent</option>
                <option value="sale">Sale</option>
            </select>
            {{-- Property --}}
            <select name="property_type_id" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-2 text-[11px] font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Properties</option>
                @foreach($propertyTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            {{-- Min Price --}}
            <input type="number" name="min_price" placeholder="Min BDT" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-[11px] font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-24">
            {{-- Max Price --}}
            <input type="number" name="max_price" placeholder="Max BDT" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-[11px] font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-24">
            {{-- Beds --}}
            <input type="number" name="bedrooms" min="0" placeholder="Beds" class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-[11px] font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-16">
            {{-- Search button --}}
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-1 rounded-lg text-[11px] font-bold shadow-sm flex items-center gap-1 transition-all">
                <i class="material-icons text-[13px]">search</i> Search
            </button>
            {{-- Results count --}}
            <span class="ml-auto text-[10px] text-gray-400 font-medium" x-text="resultsCount + ' properties found'">0 properties found</span>
        </form>
    </div>

    {{-- ==================== MAP + LISTINGS GRID ==================== --}}
    <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3">

        {{-- col-4: LISTINGS (scrollable) --}}
        <div class="col-span-12 lg:col-span-4 bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col min-h-0" style="max-height: 75vh;">
            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xs font-bold text-dark flex items-center gap-1.5">
                    <i class="material-icons text-primary text-sm">home</i> Listings
                </h2>
                <span class="text-[10px] text-gray-400 font-medium bg-gray-100 px-2 py-0.5 rounded-full" x-text="resultsCount + ' results'">0 results</span>
            </div>
            <div id="mapResults" class="flex-1 overflow-y-auto p-2.5 space-y-2 min-h-0" style="scrollbar-width:thin; overflow-y: scroll;"></div>
            <div id="mapResultsEmpty" class="hidden flex-1 flex items-center justify-center p-6">
                <div class="text-center">
                    <i class="material-icons text-gray-300 text-3xl mb-2">search_off</i>
                    <p class="text-xs font-semibold text-gray-400">No listings in this area</p>
                    <p class="text-[10px] text-gray-300 mt-0.5">Move the map or adjust filters</p>
                </div>
            </div>
        </div>

        {{-- col-8: MAP --}}
        <div class="col-span-12 lg:col-span-8 bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col">
            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xs font-bold text-dark flex items-center gap-1.5">
                    <i class="material-icons text-primary text-sm">map</i> Map View
                </h2>
                <span class="text-[10px] text-gray-400 font-medium" x-text="resultsCount + ' in view'">0 in view</span>
            </div>
            <div id="map" class="flex-1 min-h-0 bg-gray-100 rounded-b-2xl" style="position: relative; min-height: 50vh; max-height: 70vh; outline-style: none;"></div>
        </div>
    </div>

    {{-- ==================== MOBILE FILTER PANEL (bottom sheet) ==================== --}}
    <div x-show="showMobileFilters" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-black/40" @click="showMobileFilters = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[80vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            <div class="sticky top-0 bg-white px-5 pt-4 pb-3 border-b border-gray-100 flex items-center justify-between z-10">
                <h3 class="text-sm font-bold text-dark">Filters</h3>
                <button @click="showMobileFilters = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="material-icons text-gray-500 text-sm">close</i>
                </button>
            </div>
            <div class="p-5">
                <form id="mapFilterFormMobile" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Type</label>
                            <select name="listing_type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium">
                                <option value="">All</option>
                                <option value="rent">Rent</option>
                                <option value="sale">Sale</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Property</label>
                            <select name="property_type_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium">
                                <option value="">All</option>
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Min BDT</label>
                            <input type="number" name="min_price" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Max BDT</label>
                            <input type="number" name="max_price" placeholder="Any" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Bedrooms</label>
                        <input type="number" name="bedrooms" min="0" placeholder="Any" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium">
                    </div>
                    <button type="submit" class="w-full bg-primary text-white px-4 py-3 rounded-xl text-xs font-bold shadow-md shadow-primary/20">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .custom-barikoi-marker-sm { background: none !important; border: none !important; }
    .map-card { transition: all .15s ease; }
    .map-card:hover { border-color: rgba(93,135,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .leaflet-popup-content-wrapper { border-radius: 10px; padding: 0; }
    .leaflet-popup-content { margin: 0; }
</style>
@endpush

@push('scripts')
<script>
function mapSearch() {
    return {
        resultsCount: 0,
        showMobileFilters: false,
        map: null,
        markers: null,
        init() {
            const self = this;

            // Leaflet comes from a CDN. If that request fails (offline, CSP,
            // proxy) `L` is undefined and x-init would throw a ReferenceError
            // inside Alpine, leaving the map area blank with a stuck loading
            // overlay. Poll briefly for the library, then fail visibly.
            let attempts = 0;
            const timer = setInterval(function () {
                if (window.L) {
                    clearInterval(timer);
                    self.bootMap();
                } else if (++attempts >= 25) {
                    clearInterval(timer);
                    const loadingEl = document.getElementById('map-loading');
                    if (loadingEl) {
                        loadingEl.textContent = 'Map unavailable - the map library could not be loaded.';
                    }
                }
            }, 200);
        },
        bootMap() {
            const self = this;
            const loadingEl = document.getElementById('map-loading');
            const center = (window.BARIKOI_CONFIG && window.BARIKOI_CONFIG.center) || { lat: 23.8103, lng: 90.4125 };
            this.map = L.map('map', { zoomControl: false }).setView([center.lat, center.lng], 12);
            L.control.zoom({ position: 'topright' }).addTo(this.map);

            const OSM_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const OSM_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OSM</a>';

            function applyTiles(config) {
                const tileUrl = config && config.tile_url;
                if (tileUrl) {
                    L.tileLayer(tileUrl, { maxZoom: 19, attribution: '&copy; Barikoi' }).addTo(self.map);
                } else {
                    L.tileLayer(OSM_URL, { maxZoom: 19, attribution: OSM_ATTRIBUTION }).addTo(self.map);
                }
                if (loadingEl) loadingEl.style.display = 'none';
                self.loadListings(self.map.getBounds());
            }

            applyTiles(window.BARIKOI_CONFIG);
            this.markers = L.layerGroup().addTo(this.map);
            this.map.on('load', function() {
                setTimeout(function() { self.map.invalidateSize(); }, 100);
            });
            this.map.on('moveend', () => self.loadListings(self.map.getBounds()));

            const filterForm = document.getElementById('mapFilterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', e => { e.preventDefault(); self.loadListings(self.map.getBounds()); });
            }
            const mobileForm = document.getElementById('mapFilterFormMobile');
            if (mobileForm) {
                mobileForm.addEventListener('submit', e => { e.preventDefault(); self.loadListings(self.map.getBounds()); self.showMobileFilters = false; });
            }
        },
        loadListings(bounds) {
            const self = this;
            const bbox = bounds ? bounds.getSouth()+','+bounds.getWest()+','+bounds.getNorth()+','+bounds.getEast() : '';
            const form = document.getElementById('mapFilterForm');
            const params = form ? new URLSearchParams(new FormData(form)) : new URLSearchParams();
            if (bbox) params.set('bbox', bbox);
            fetch('{{ route("ajax.search") }}?'+params).then(r => r.json()).then(data => {
                self.markers.clearLayers();
                const resultsDiv = document.getElementById('mapResults');
                const emptyDiv = document.getElementById('mapResultsEmpty');
                if (resultsDiv) resultsDiv.innerHTML = '';
                const listings = data.data || [];
                self.resultsCount = listings.length;

                if (emptyDiv) {
                    if (listings.length === 0) {
                        if (resultsDiv) resultsDiv.classList.add('hidden');
                        emptyDiv.classList.remove('hidden');
                    } else {
                        if (resultsDiv) resultsDiv.classList.remove('hidden');
                        emptyDiv.classList.add('hidden');
                    }
                }

                const primaryIcon = L.divIcon({
                    className: 'custom-barikoi-marker-sm',
                    html: '<div style="width:26px;height:26px;background:#5d87ff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);color:#fff;font-size:9px;font-weight:bold;">&#2547;</span></div>',
                    iconSize: [26, 26], iconAnchor: [13, 26], popupAnchor: [0, -26],
                });

                const highlightIcon = L.divIcon({
                    className: 'custom-barikoi-marker-sm',
                    html: '<div style="width:30px;height:30px;background:#ff4757;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);color:#fff;font-size:10px;font-weight:bold;">&#2547;</span></div>',
                    iconSize: [30, 30], iconAnchor: [15, 30], popupAnchor: [0, -30],
                });

                listings.forEach(listing => {
                    if (listing.lat && listing.lng) {
                        const price = Number(listing.price).toLocaleString();
                        const addr = listing.address || '';
                        const pType = listing.property_type_name || '';

                        const marker = L.marker([listing.lat, listing.lng], { icon: primaryIcon }).addTo(self.markers);
                        marker.bindPopup(
                            '<div style="padding:10px;font-family:system-ui;min-width:150px;">' +
                                '<div style="font-size:12px;font-weight:700;color:#1a1a2e;margin-bottom:4px;">' + listing.title + '</div>' +
                                '<div style="font-size:14px;font-weight:800;color:#5d87ff;">&#2547;' + price + '</div>' +
                                (pType ? '<div style="font-size:10px;color:#666;margin-top:2px;">' + pType + '</div>' : '') +
                                '<a href="' + listing.url + '" style="display:inline-block;margin-top:6px;padding:3px 10px;background:#5d87ff;color:#fff;border-radius:6px;font-size:10px;font-weight:600;text-decoration:none;">View</a>' +
                            '</div>'
                        );
                        marker.on('mouseover', function() { this.openPopup(); });

                        if (resultsDiv) {
                            const card = document.createElement('a');
                            card.href = listing.url;
                            card.dataset.lat = listing.lat;
                            card.dataset.lng = listing.lng;
                            card.className = 'map-card block bg-white border border-gray-100 rounded-xl p-3 cursor-pointer';
                            card.innerHTML =
                                '<div class="flex gap-3">' +
                                    '<div class="w-14 h-14 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">' +
                                        (listing.primary_image_url
                                            ? '<img src="' + listing.primary_image_url + '" alt="" class="w-full h-full object-cover">'
                                            : '<i class="material-icons text-gray-300 text-lg">home</i>') +
                                    '</div>' +
                                    '<div class="flex-1 min-w-0">' +
                                        '<h4 class="text-[11px] font-bold text-dark truncate">' + listing.title + '</h4>' +
                                        '<p class="text-xs font-extrabold text-primary mt-0.5">&#2547;' + price + '</p>' +
                                        '<p class="text-[10px] text-gray-400 mt-0.5 truncate">' + addr + '</p>' +
                                    '</div>' +
                                '</div>';
                            card.addEventListener('mouseenter', function() {
                                self.markers.eachLayer(m => { if (m.getLatLng && m.getLatLng().lat == this.dataset.lat) m.setIcon(highlightIcon); });
                            });
                            card.addEventListener('mouseleave', function() {
                                self.markers.eachLayer(m => { if (m.getLatLng && m.getLatLng().lat == this.dataset.lat) m.setIcon(primaryIcon); });
                            });
                            resultsDiv.appendChild(card);
                        }
                    }
                });
            });
        }
    }
}
</script>
@endpush
@endsection
