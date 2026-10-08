{{-- resources/views/components/layouts/nav.blade.php --}}
@php
    $categories = [
        ['name' => 'Family', 'slug' => 'family', 'icon' => 'family_restroom'],
        ['name' => 'Bachelor', 'slug' => 'bachelor', 'icon' => 'school'],
        ['name' => 'Office', 'slug' => 'office', 'icon' => 'business_center'],
        ['name' => 'Sublet', 'slug' => 'sublet', 'icon' => 'bedroom_parent'],
        ['name' => 'Hostel', 'slug' => 'hostel', 'icon' => 'hotel'],
        ['name' => 'Shop', 'slug' => 'shop', 'icon' => 'storefront'],
    ];

    $navLinks = [
        ['name' => 'Home', 'href' => route('home'), 'icon' => 'home'],
        [
            'name' => 'Property List',
            'href' => route('search'),
            'icon' => 'apartment',
            'hasDropdown' => true,
            'dropdownItems' => collect($navDivisions ?? [])->map(fn($d) => [
                'name' => $d->name,
                'href' => route('division.show', $d->slug),
            ])->prepend(['name' => 'All Properties', 'href' => route('search')])->values()->toArray(),
        ],
        ['name' => 'Earn Money', 'href' => 'programs/affiliate/earn-money', 'icon' => 'account_balance_wallet'],
        ['name' => 'Article', 'href' => route('blog.index'), 'icon' => 'article'],
        ['name' => 'Career', 'href' => 'career/feeds', 'icon' => 'work'],
        ['name' => 'Order Home', 'href' => 'create/order/home', 'icon' => 'home_work'],
    ];

    $divisionsData = $navDivisions ?? collect();
@endphp

<div x-data="{
    mobileOpen: false,
    isSearchOpen: false,
    searchQuery: '',
    searchCategory: '',
    propertyId: '',
    locationSearch: '',
    barikoiResults: [],
    barikoiSearching: false,
    selectedDivision: '',
    selectedDistrict: '',
    selectedArea: '',
    districts: [],
    areas: [],
    divisionDistrictMap: @js($divisionDistrictMap ?? []),
    districtAreaMap: @js($districtAreaMap ?? []),

    /**
     * Build the search URL from the current modal state and navigate.
     * Property ID maps to the `property_id` query parameter.
     */
    submitSearch() {
        const params = new URLSearchParams();
        if (this.propertyId) params.set('property_id', this.propertyId);
        if (this.searchQuery) params.set('q', this.searchQuery);
        if (this.searchCategory) params.set('type', this.searchCategory);
        if (this.selectedDivision) params.set('division_id', this.selectedDivision);
        if (this.selectedDistrict) params.set('district_id', this.selectedDistrict);
        if (this.selectedArea) params.set('area_id', this.selectedArea);
        if (this.locationSearch) params.set('loc', this.locationSearch);
        this.isSearchOpen = false;
        window.location.href = '{{ route('search') }}?' + params.toString();
    },

    /** Clear every field and close the modal. */
    resetSearch() {
        this.propertyId = '';
        this.searchQuery = '';
        this.searchCategory = '';
        this.locationSearch = '';
        this.barikoiResults = [];
        this.selectedDivision = '';
        this.selectedDistrict = '';
        this.selectedArea = '';
        this.districts = [];
        this.areas = [];
        this.isSearchOpen = false;
    }
}" @keydown.escape.window="isSearchOpen = false; mobileOpen = false">

<header class="bg-white w-full z-[60] relative shadow-sm">

    {{-- ROW 1: Top Bar — Branding & Actions --}}
    <div class="border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 h-[55px] flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center shrink-0">
                <img src="{{ asset('images/logo.png') }}" loading="lazy" alt="ToLetKoi" class="h-8 w-auto">
            </a>

            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard.listings.create') }}" class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-medium text-gray-600 hover:border-primary hover:text-primary transition-colors">
                        <i class="material-icons text-base">add</i> Add Property
                    </a>
                    <a href="{{ route('dashboard.listings.create') }}" class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-medium text-gray-600 hover:border-primary hover:text-primary transition-colors">
                        <i class="material-icons text-base opacity-70">home_work</i> Order Home
                    </a>
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-dark transition-colors">
                            <i class="material-icons text-lg">person</i>
                        </button>
                        <div x-show="profileOpen" x-transition x-cloak class="absolute top-full right-0 mt-2 w-52 bg-white border border-gray-100 rounded-b-lg shadow-lg z-[999] py-2">
                            <div class="px-4 py-2 border-b border-gray-50">
                                <p class="text-[11px] font-bold text-dark">{{ auth()->user()->name ?? 'Account' }}</p>
                            </div>
                            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                                <i class="material-icons text-[14px]">dashboard</i> Dashboard
                            </a>
                            <a href="{{ route('dashboard.listings.create') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                                <i class="material-icons text-[14px]">add_home</i> Post Property
                            </a>
                            <a href="{{ route('dashboard.favorites.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                                <i class="material-icons text-[14px]">favorite_border</i> Saved
                            </a>
                            <a href="{{ route('dashboard.profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                                <i class="material-icons text-[14px]">settings</i> Profile
                            </a>
                            <hr class="my-1 border-gray-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                    <i class="material-icons text-[14px]">logout</i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-gray-600 hover:text-primary transition-colors">
                        Login / Register
                    </a>
                @endauth
            </div>

            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-dark">
                <i class="material-icons text-xl" x-text="mobileOpen ? 'close' : 'menu'"></i>
            </button>
        </div>
    </div>

    {{-- ROW 2: Main Navigation --}}
    <div class="border-b border-gray-100 hidden md:block">
        <div class="max-w-6xl mx-auto px-4 h-[40px] flex items-center justify-between">
            <nav class="flex items-center gap-5 h-full">
                @foreach($navLinks as $link)
                    <div class="relative h-full flex items-center" @if($link['hasDropdown'] ?? false) x-data="{ subDropdown: false }" @endif>
                        @if($link['hasDropdown'] ?? false)
                            <button
                                @click="subDropdown = !subDropdown"
                                class="flex items-center gap-1 text-xs font-medium h-full border-b-2 transition-all px-1 cursor-pointer
                                    {{ request()->is('search*') || request()->is('listing*') ? 'text-primary border-primary' : 'text-gray-600 border-transparent hover:text-primary hover:border-gray-200' }}"
                            >
                                <i class="material-icons text-[15px] opacity-80">{{ $link['icon'] }}</i>
                                {{ $link['name'] }}
                                <i class="material-icons text-[14px]">keyboard_arrow_down</i>
                            </button>
                            <div
                                x-show="subDropdown"
                                @click.away="subDropdown = false"
                                x-cloak
                                x-transition
                                class="absolute top-full left-0 w-48 bg-white border border-gray-100 rounded-b-lg shadow-lg z-[999] py-2"
                            >
                                @foreach($link['dropdownItems'] as $item)
                                    <a href="{{ $item['href'] }}" class="block px-4 py-2 text-xs text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                                        {{ $item['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <a href="{{ $link['href'] }}"
                               class="flex items-center gap-1 text-xs font-medium h-full border-b-2 transition-all px-1
                                    {{ request()->is(trim(parse_url($link['href'], PHP_URL_PATH), '/')) && parse_url($link['href'], PHP_URL_PATH) !== '/' ? 'text-primary border-primary' : (request()->is('/') && $link['href'] === route('home') ? 'text-primary border-primary' : 'text-gray-600 border-transparent hover:text-primary hover:border-gray-200') }}">
                                <i class="material-icons text-[15px] opacity-80">{{ $link['icon'] }}</i>
                                {{ $link['name'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </nav>

            <button
                @click="isSearchOpen = true"
                class="flex items-center gap-1.5 text-gray-500 hover:text-primary transition-colors pr-1"
            >
                <i class="material-icons text-[16px]">search</i>
                <span class="text-xs font-medium">Search</span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition x-cloak @click.away="mobileOpen = false"
         class="md:hidden bg-white border-b border-gray-100 shadow-lg">
        <div class="p-4 space-y-3">
            @foreach($navLinks as $link)
                @if($link['hasDropdown'] ?? false)
                    <div x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-2 text-sm font-medium text-gray-700 hover:text-primary">
                            <span class="flex items-center gap-2">
                                <i class="material-icons text-[16px] text-gray-400">{{ $link['icon'] }}</i> {{ $link['name'] }}
                            </span>
                            <i class="material-icons text-sm transition-transform" :class="subOpen ? 'rotate-90' : ''">chevron_right</i>
                        </button>
                        <div x-show="subOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" x-cloak class="pl-6 space-y-0.5">
                            @foreach($link['dropdownItems'] as $item)
                                <a href="{{ $item['href'] }}" class="block py-1.5 text-sm text-gray-500 hover:text-primary">{{ $item['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $link['href'] }}" class="block py-2 text-sm font-medium text-gray-700 hover:text-primary">
                        {{ $link['name'] }}
                    </a>
                @endif
            @endforeach
            <hr>
            @auth
                <a href="{{ route('dashboard.index') }}" class="block py-2 text-sm font-medium text-primary">Dashboard</a>
                <a href="{{ route('dashboard.listings.create') }}" class="block py-2 text-sm font-medium text-primary">Post Property</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block py-2 text-sm font-medium text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-sm font-medium text-primary">Login / Register</a>
            @endauth
        </div>
    </div>
</header>

{{-- SEARCH MODAL (compact design matching reference) --}}
<div
    x-show="isSearchOpen"
    x-cloak
    x-transition.opacity
    @keydown.escape.window="isSearchOpen = false"
    class="fixed inset-0 z-[100] bg-dark/60 backdrop-blur-sm flex items-start justify-center pt-16 animate-fade-in overflow-y-auto"
>
    <div class="absolute inset-0" @click="isSearchOpen = false"></div>

    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl relative z-10 mx-4 overflow-hidden mb-10">
        {{-- Header --}}
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-dark">Property Search</h2>
            <button type="button" @click="isSearchOpen = false"
                    class="w-6 h-6 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-dark transition-colors"
                    aria-label="Close search">
                <i class="material-icons text-[16px] leading-none" aria-hidden="true">close</i>
            </button>
        </div>

        <div class="p-4">
            {{-- Search by keyword --}}
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-dark mb-1">Search</label>
                <div class="relative flex-center">
                    <i class="material-icons absolute left-3 text-gray-300 text-[16px]">search</i>
                    <input
                        type="text"
                        x-model="propertyId"
                        @keydown.enter="submitSearch()"
                        placeholder="Search by keyword or Property ID..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-dark placeholder:text-gray-400 focus:outline-none focus:border-primary transition"
                    >
                </div>
            </div>

            {{-- Category --}}
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-dark mb-2">Category</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($categories as $cat)
                        <button
                            type="button"
                            @click="searchCategory = searchCategory === '{{ $cat['slug'] }}' ? '' : '{{ $cat['slug'] }}'"
                            :class="searchCategory === '{{ $cat['slug'] }}'
                                ? 'border-primary bg-primary text-white'
                                : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary hover:bg-primary/5'"
                            class="px-3 py-1.5 border rounded-lg text-[12px] font-medium transition-all"
                        >
                            <i class="material-icons text-[16px] mr-1 align-middle">{{ $cat['icon'] }}</i>{{ $cat['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Location (Barikoi autocomplete) --}}
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-dark mb-1">Location</label>
                <div class="relative flex-center">
                    <i class="material-icons absolute left-3 text-gray-300 text-[16px]">place</i>
                    <input
                        type="text"
                        x-model="locationSearch"
                        @keydown.enter="submitSearch()"
                        @input.debounce.400ms="
                            barikoiSearching = true;
                            barikoiResults = [];
                            if (locationSearch.length >= 2) {
                                fetch('/api/maps/autocomplete?q=' + encodeURIComponent(locationSearch))
                                    .then(r => r.json())
                                    .then(data => {
                                        barikoiResults = (data.places || []).slice(0, 6);
                                        barikoiSearching = false;
                                    })
                                    .catch(() => barikoiSearching = false);
                            } else {
                                barikoiSearching = false;
                            }
                        "
                        placeholder="Search by location..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-dark placeholder:text-gray-400 focus:outline-none focus:border-primary transition"
                    >
                    {{-- Barikoi Autocomplete Dropdown --}}
                    <template x-if="barikoiResults.length > 0">
                        <div class="absolute top-full left-0 w-full mt-1 bg-white border border-gray-100 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50">
                            <template x-for="(place, idx) in barikoiResults" :key="idx">
                                <button @click="
                                    locationSearch = place.address || place.address_bn || '';
                                    barikoiResults = [];
                                " class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center gap-2 border-b border-gray-50 last:border-0 transition-colors">
                                    <i class="material-icons text-gray-300 text-[12px]">place</i>
                                    <div>
                                        <div class="text-[10px] font-medium text-dark" x-text="place.address || place.address_bn || ''"></div>
                                        <div class="text-[9px] text-gray-400" x-text="[place.area, place.district, place.division].filter(Boolean).join(', ')"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Division / District --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-bold text-dark mb-1">Division</label>
                    <div class="relative">
                        <select
                            x-model="selectedDivision"
                            @change="selectedDistrict = ''; selectedArea = ''; districts = divisionDistrictMap[$event.target.value] || []; areas = [];"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-500 focus:outline-none focus:border-primary transition cursor-pointer"
                        >
                            <option value="">All Divisions</option>
                            @foreach($divisionsData as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-dark mb-1">District</label>
                    <div class="relative">
                        <select
                            x-model="selectedDistrict"
                            :disabled="!selectedDivision"
                            @change="selectedArea = ''; areas = districtAreaMap[$event.target.value] || [];"
                            :class="!selectedDivision ? 'bg-gray-50 cursor-not-allowed' : 'cursor-pointer'"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-500 focus:outline-none focus:border-primary disabled:bg-gray-50 disabled:cursor-not-allowed transition"
                        >
                            <option value="" x-text="!selectedDivision ? 'Select division first' : 'Select district'"></option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Area --}}
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-dark mb-1">Area</label>
                <div class="relative">
                    <select
                        x-model="selectedArea"
                        :disabled="!selectedDistrict"
                        :class="!selectedDistrict ? 'bg-gray-50 cursor-not-allowed' : 'cursor-pointer'"
                        class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-500 focus:outline-none focus:border-primary disabled:bg-gray-50 disabled:cursor-not-allowed transition"
                    >
                        <option value="" x-text="!selectedDistrict ? 'Select district first' : 'Select area'"></option>
                        <template x-for="a in areas" :key="a.id">
                            <option :value="a.id" x-text="a.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Keyword --}}
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-dark mb-1">Keyword</label>
                <div class="relative flex-center">
                    <i class="material-icons absolute left-3 text-gray-300 text-[16px]">text_fields</i>
                    <input
                        type="text"
                        x-model="searchQuery"
                        @keydown.enter="submitSearch()"
                        placeholder="e.g. 3 bedroom flat..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs font-medium text-dark placeholder:text-gray-400 focus:outline-none focus:border-primary transition"
                    >
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
            <button type="button" @click="resetSearch()"
                    class="text-xs font-medium text-gray-400 hover:text-dark px-2 transition-colors">
                Clear All
            </button>
            <button type="button" @click="submitSearch()"
                    class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg font-bold text-xs shadow-sm shadow-primary/30 flex items-center gap-1 transition-colors">
                <i class="material-icons text-sm">search</i> Search
            </button>
        </div>
    </div>
</div></div>
