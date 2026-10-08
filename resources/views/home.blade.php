@extends('layouts.app')

@section('content')
    <div x-data="{
        activeCategory: 'All',
        searchQuery: '',
        locationSearch: '',
        barikoiResults: [],
        barikoiSearching: false,
    }" class="bg-[#f2f4f8] min-h-screen text-dark font-sans text-sm">

        {{-- 1. HERO with Search --}}
        <section class="bg-gradient-to-br from-primary/5 to-primary/10 py-8 md:py-12">
            <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center gap-8">
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-2xl md:text-4xl font-bold text-dark mb-2 leading-tight">
                        সহজেই<br>বাসা খুঁজুন<br>
                        <span class="text-primary">সারা বাংলাদেশে</span>
                    </h1>
                    <p class="text-sm text-gray-500 mb-6">বাংলাদেশের সবচেয়ে বিশ্বস্ত প্রপার্টি এবং রেন্টাল প্ল্যাটফর্ম</p>

                    {{-- Pill-shaped Search Bar --}}
                    <div
                        class="max-w-lg mx-auto md:mx-0 bg-white rounded-full shadow-lg border border-gray-200 p-1 flex items-center">
                        <div class="flex-1 px-4 py-2 relative">
                            <input type="text" placeholder="Search by location..." x-model="locationSearch"
                                @input.debounce.400ms="
                                barikoiSearching = true;
                                barikoiResults = [];
                                if (locationSearch.length >= 2) {
                                    fetch('/api/maps/autocomplete?q=' + encodeURIComponent(locationSearch))
                                        .then(r => r.json())
                                        .then(data => {
                                            barikoiResults = (data.places || []).slice(0, 8);
                                            barikoiSearching = false;
                                        })
                                        .catch(() => barikoiSearching = false);
                                } else {
                                    barikoiSearching = false;
                                }
                            "
                                class="w-full text-xs text-dark placeholder-gray-400 focus:outline-none bg-transparent">
                            {{-- Barikoi Autocomplete --}}
                            <template x-if="barikoiResults.length > 0">
                                <div
                                    class="absolute top-full left-0 w-72 mt-1 bg-white border border-gray-100 rounded-xl shadow-lg max-h-60 overflow-y-auto z-50">
                                    <template x-for="(place, idx) in barikoiResults" :key="idx">
                                        <button
                                            @click="
                                        locationSearch = place.address || place.address_bn || '';
                                        barikoiResults = [];
                                        window.location.href = '{{ route('search') }}?loc=' + encodeURIComponent(place.area || place.address || locationSearch);
                                    "
                                            class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center gap-2 border-b border-gray-50 last:border-0 transition-colors">
                                            <i class="material-icons text-gray-300 text-[14px]">place</i>
                                            <div>
                                                <div class="text-xs font-medium text-dark"
                                                    x-text="place.address || place.address_bn || ''"></div>
                                                <div class="text-[10px] text-gray-400"
                                                    x-text="[place.area, place.district, place.division].filter(Boolean).join(', ')">
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <a href="{{ route('search') }}"
                            class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="material-icons text-sm">search</i>
                            <span class="text-xs font-bold hidden sm:inline">Search</span>
                        </a>
                    </div>
                </div>
                <div class="flex-1 hidden md:flex justify-center">
                    <div class="relative w-64 h-52">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=400&q=80"
                            alt="ToLetKoi" class="w-full h-full object-cover rounded-2xl shadow-lg" loading="eager">
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-lg p-3 flex items-center gap-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <i class="material-icons text-primary text-xl">verified</i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-dark">Verified Listings</p>
                                <p class="text-[10px] text-gray-400">100% Authentic</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. FEATURED PROPERTY LISTINGS --}}
        <section class="py-6 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <div class="items-start mb-3 border-l-4 border-primary pl-2">
                    <h2 class="text-sm font-bold text-dark">
                        Featured Properties
                    </h2>
                    <p class="text-xs text-black-50">Browse the best available listings.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    @php
                        $allListings = $featuredListings->take(24);
                    @endphp
                    @foreach ($allListings as $listing)
                        @php
                            $images = $listing->images?->count()
                                ? $listing->images->map(fn($img) => Storage::disk('public')->url($img->path))->toArray()
                                : [
                                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80',
                                ];
                            $image = $images[0];
                            $location = $listing->area?->name ?? $listing->address ?? 'Dhaka';
                            $location = $listing->area ? $location . ', ' . ($listing->area->district?->name ?? '') : $location;
                        @endphp
                        <a href="{{ $listing->getUrl() }}"
                            class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 group flex flex-col h-full overflow-hidden">
                            <div class="relative h-32 overflow-hidden bg-gray-100">
                                <img src="{{ $image }}" alt="{{ $listing->title }}" loading="lazy" decoding="async"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute bottom-2 right-2">
                                    <span
                                        class="bg-primary/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                                        ৳ {{ number_format($listing->price) }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-2.5 flex flex-col flex-1">
                                <h3
                                    class="font-bold text-[11px] text-dark mb-1 leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $listing->title }}</h3>
                                <p class="text-gray-400 text-[10px] mb-2 flex items-center gap-1 truncate">
                                    <i class="material-icons text-[10px]">place</i> {{ $location }}
                                </p>
                                <div
                                    class="flex items-center gap-2 mt-auto pt-2 border-t border-gray-50 text-[12px] text-gray-500 font-medium">
                                    @if ($listing->bedrooms)
                                        <span>Room: {{ $listing->bedrooms }}</span>
                                    @endif
                                    @if ($listing->bathrooms)
                                        <span class="text-gray-300"> - </span>
                                        <span>Bath: {{ $listing->bathrooms }}</span>
                                    @endif
                                </div>
                                <div
                                    class="flex items-start gap-2 mt-auto pt-2 border-t border-gray-50 text-[12px] text-gray-500 font-medium">
                                    @if ($listing->available_from)
                                        <span>To-Let From: {{ date_format($listing->available_from, "M Y") }}</span>
                                    @endif
                                </div>

                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 3. JOB FEED --}}
        <section class="py-6 bg-[#f2f4f8]">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-sm font-bold text-dark">Job Feed</h2>
                    <a href="{{ route('pricing') }}" class="text-[10px] font-bold text-primary hover:underline">View
                        All</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    @php
                        $jobFeedTypes = [
                            ['name' => 'Family Room', 'icon' => 'family_restroom', 'sub' => 'Find the best deals'],
                            ['name' => 'Bachelor Room', 'icon' => 'school', 'sub' => 'Find the best deals'],
                            ['name' => 'Office Space', 'icon' => 'business_center', 'sub' => 'Find the best deals'],
                            ['name' => 'Sublet Room', 'icon' => 'bedroom_parent', 'sub' => 'Find the best deals'],
                            ['name' => 'Hostel Room', 'icon' => 'hotel', 'sub' => 'Find the best deals'],
                            ['name' => 'Shop Space', 'icon' => 'storefront', 'sub' => 'Find the best deals'],
                        ];
                    @endphp
                    @foreach ($jobFeedTypes as $job)
                        <div
                            class="bg-white rounded-xl p-3 border border-gray-100 hover:border-primary hover:shadow-sm transition-all cursor-pointer">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mb-2">
                                <i class="material-icons text-primary text-lg">{{ $job['icon'] }}</i>
                            </div>
                            <h4 class="font-bold text-[11px] text-dark mb-0.5">Looking for {{ $job['name'] }}</h4>
                            <p class="text-[10px] text-gray-400">{{ $job['sub'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 4. RECENTLY PUBLISHED BY CATEGORY --}}
        <section class="py-3 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-sm font-bold text-dark text-center">Recently Published Properties</h2>
                <p class="text-[10px] text-gray-400 text-center mb-0">Property list on ToLetKoi - Properties of your choice
                </p>
            </div>
        </section>

        @php
            /*
             * Homepage category rails.
             *
             * `listings` has no category column and property_type names are
             * Apartment/House/Room/Office Space/..., so the previous filters on
             * "family"/"bachelor" never matched anything and every rail rendered
             * empty. Each rail is now backed by the real property types that make
             * up that category, matched on slug.
             */
            $categorySections = [
                ['title' => 'Family', 'type' => 'family', 'slugs' => ['house', 'apartment']],
                ['title' => 'Bachelor', 'type' => 'bachelor', 'slugs' => ['room']],
                ['title' => 'Office', 'type' => 'office', 'slugs' => ['office-space']],
                ['title' => 'Shop', 'type' => 'shop', 'slugs' => ['shop']],
                ['title' => 'Land', 'type' => 'land', 'slugs' => ['land']],
            ];

            foreach ($categorySections as $i => $section) {
                $categorySections[$i]['listings'] = $recentListings
                    ->filter(fn($l) => in_array(strtolower($l->propertyType?->slug ?? ''), $section['slugs'], true))
                    ->take(6);
            }
        @endphp

        @foreach ($categorySections as $section)
            @if ($section['listings']->count())
                <section class="py-3 bg-white">
                    <div class="max-w-6xl mx-auto px-4">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-sm font-bold text-dark">{{ $section['title'] }}</h2>
                            <a href="{{ route('search', ['type' => $section['type']]) }}"
                                class="text-[10px] font-bold text-primary hover:underline flex items-center gap-0.5">
                                See More {{ $section['title'] }} Property <i
                                    class="material-icons text-[12px]">chevron_right</i>
                            </a>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                            @foreach ($section['listings'] as $listing)
                                @php
                                    $images = $listing->images?->count()
                                        ? $listing->images
                                            ->map(fn($img) => Storage::disk('public')->url($img->path))
                                            ->toArray()
                                        : [
                                            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80',
                                        ];
                                    $image = $images[0];
                                    $location = $listing->area?->name ?? $listing->address ?? 'Dhaka';
                                    $location = $listing->area ? $location . ', ' . ($listing->area->district?->name ?? '') : $location;
                                @endphp
                                <a href="{{ $listing->getUrl() }}"
                                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 group flex flex-col h-full overflow-hidden">
                                    <div class="relative h-32 overflow-hidden bg-gray-100">
                                        <img src="{{ $image }}" alt="{{ $listing->title }}" loading="lazy"
                                            decoding="async"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute bottom-2 right-2">
                                            <span
                                                class="bg-primary/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                                                ৳ {{ number_format($listing->price) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-2.5 flex flex-col flex-1">
                                        <h3
                                            class="font-bold text-[11px] text-dark mb-1 leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                            {{ $listing->title }}</h3>
                                        <p class="text-gray-400 text-[10px] mb-2 flex items-center gap-1 truncate">
                                            <i class="material-icons text-[10px]">place</i> {{ $location }}
                                        </p>
                                        <div
                                            class="flex items-center gap-2 mt-auto pt-2 border-t border-gray-50 text-[12px] text-gray-500 font-medium">
                                            @if ($listing->bedrooms)
                                                <span>Room: {{ $listing->bedrooms }}</span>
                                            @endif
                                            @if ($listing->bathrooms)
                                                <span class="text-gray-300"> - </span>
                                                <span>Bath: {{ $listing->bathrooms }}</span>
                                            @endif
                                        </div>
                                        <div
                                            class="flex items-start gap-2 mt-auto pt-2 border-t border-gray-50 text-[12px] text-gray-500 font-medium">
                                            @if ($listing->available_from)
                                                <span>To-Let From: {{ date_format($listing->available_from, "M Y") }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endforeach

        {{-- 5. MOST POPULAR PLACES --}}
        @if ($popularAreas->count())
            <section class="py-8 bg-[#f2f4f8]">
                <div class="max-w-6xl mx-auto px-4">
                    <h2 class="text-sm font-bold text-dark text-center mb-4">Most Popular Places</h2>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 md:p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3">
                            @foreach ($popularAreas as $area)
                                <a href="{{ route('area.show', ['division' => $area->district->division->slug ?? $area->district->division->id, 'district' => $area->district->slug ?? $area->district->id, 'area' => $area->slug ?? $area->id]) }}"
                                    class="flex items-start gap-2 group">
                                    <i class="material-icons text-primary text-[16px] mt-0.5 shrink-0">location_on</i>
                                    <div>
                                        <span
                                            class="text-primary group-hover:underline text-xs font-medium block leading-tight">
                                            {{ $area->name_bn ?? $area->name }},
                                            {{ $area->district?->name_bn ?? ($area->district?->name ?? 'ঢাকা') }}
                                        </span>
                                        <span
                                            class="text-[10px] text-gray-400">{{ number_format($area->property_count ?? 0) }}
                                            টি প্রপার্টি</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- 6. STATS --}}
        <section class="py-10 bg-white text-center border-t border-gray-100">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-xl font-bold text-dark mb-1">A Decade of Excellence in Real Estate</h2>
                <p class="text-gray-500 text-[10px] mb-6 max-w-lg mx-auto">Trusted by millions for our transparent and
                    reliable service.</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @php
                        $statsItems = [
                            [
                                'label' => 'Happy Clients',
                                'val' => $stats['total_listings'] ?? 12,
                                'icon' => 'emoji_events',
                            ],
                            [
                                'label' => 'Property Locations',
                                'val' => $stats['total_areas'] ?? 100,
                                'icon' => 'location_city',
                            ],
                            [
                                'label' => 'Total Listings',
                                'val' => $stats['total_listings'] ?? 12,
                                'icon' => 'apartment',
                            ],
                            [
                                'label' => 'Awards',
                                'val' => $stats['total_divisions'] ?? 4,
                                'icon' => 'workspace_premium',
                            ],
                        ];
                    @endphp
                    @foreach ($statsItems as $stat)
                        <div class="flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
                            <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="material-icons text-primary text-2xl">{{ $stat['icon'] }}</i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-dark leading-tight">{{ $stat['val'] }}+</div>
                                <div class="text-xs text-gray-500">{{ $stat['label'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 7. RECENT ARTICLES --}}
        @if ($recentArticles->count())
            <section class="py-8 bg-[#f2f4f8]">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-sm font-bold text-dark border-l-4 border-primary pl-2">Recent Articles</h2>
                        <a href="{{ route('blog.index') }}"
                            class="text-[10px] font-bold text-primary hover:underline">View All</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($recentArticles as $article)
                            <a href="{{ route('blog.show', $article->slug ?? $article->id) }}"
                                class="bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-md transition-shadow group">
                                <div class="h-32 bg-gray-100 overflow-hidden">
                                    @if ($article->featured_image)
                                        <img src="{{ Storage::disk('public')->url($article->featured_image) }}"
                                            alt="{{ $article->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            loading="lazy">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
                                            <i class="material-icons text-primary/30 text-4xl">article</i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3
                                        class="font-bold text-xs text-dark group-hover:text-primary transition-colors line-clamp-2 mb-1">
                                        {{ $article->title }}</h3>
                                    <p class="text-[10px] text-gray-400 line-clamp-2">
                                        {{ Str::limit(strip_tags($article->content ?? ''), 100) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- 8. APP DOWNLOAD CTA --}}
        <section class="bg-primary overflow-hidden relative py-10">
            <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center gap-8">
                <div class="flex-1 text-center md:text-left text-white z-10">
                    <h2 class="text-xl font-bold mb-2">Get the ToLetKoi app and enjoy amazing features</h2>
                    <p class="text-white/80 text-xs mb-4 leading-relaxed max-w-md">
                        Property Listings • Rent • Buy • Sell • Verified • Easy & Safe • Simple & Smart Filters • Fast
                        Customer Support
                    </p>
                    <div class="flex justify-center md:justify-start gap-3">
                        <button
                            class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-900 transition-colors">
                            <i class="material-icons text-xl">apple</i>
                            <div class="text-left leading-none">
                                <div class="text-[9px]">Download on the</div>
                                <div class="text-xs font-bold">App Store</div>
                            </div>
                        </button>
                        <button
                            class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-900 transition-colors">
                            <i class="material-icons text-xl">android</i>
                            <div class="text-left leading-none">
                                <div class="text-[9px]">Get it on</div>
                                <div class="text-xs font-bold">Google Play</div>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="flex-1 hidden md:flex justify-center">
                    <div
                        class="w-40 h-72 bg-black rounded-t-[2.5rem] border-4 border-gray-700 relative overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=300&q=80"
                            class="w-full h-full object-cover opacity-70" loading="lazy" alt="ToLetKoi App">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center">
                                <span class="text-primary font-bold text-xl">T</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 9. FEEDBACK / TESTIMONIALS --}}
        @php
            $testimonials = [
                [
                    'name' => 'রহিম উদ্দিন',
                    'role' => 'Landlord',
                    'rating' => 5,
                    'text' => 'টোলেটকয় আমাকে অনেক সুবিধা দিয়েছে। আমার প্রপার্টি দ্রুত ভাড়া দিতে সক্ষম হয়েছি।',
                ],
                [
                    'name' => 'ফাতেমা আক্তার',
                    'role' => 'Tenant',
                    'rating' => 5,
                    'text' => 'ব্রোকার ছাড়াই বাসা খুঁজে পেয়েছি। অনেক ধন্যবাদ টোলেটকয় টিমকে।',
                ],
                [
                    'name' => 'কামরুল হাসান',
                    'role' => 'User',
                    'rating' => 5,
                    'text' => 'খুবই সহজ এবং বিশ্বস্ত প্ল্যাটফর্ম। সবাইকে সুপারিশ করি।',
                ],
            ];
        @endphp
        <section class="py-10 bg-[#f2f4f8]">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-sm font-bold text-dark text-center mb-1">Feedback</h2>
                <p class="text-[10px] text-gray-400 text-center mb-4">We highly value the feedback of our customers</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($testimonials as $t)
                        <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-1 mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="material-icons text-[12px] {{ $i <= $t['rating'] ? 'text-yellow-400' : 'text-gray-200' }}">star</i>
                                @endfor
                            </div>
                            <p class="text-[11px] text-gray-600 mb-3 leading-relaxed line-clamp-3">{{ $t['text'] }}</p>
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-7 h-7 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ mb_substr($t['name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-dark">{{ $t['name'] }}</p>
                                    <p class="text-[9px] text-gray-400">{{ $t['role'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 10. OUR PURPOSE --}}
        <section class="py-10 bg-[#f2f4f8] text-center">
            <div class="max-w-3xl mx-auto px-4">
                <h2 class="text-sm font-bold text-dark mb-3">Our Purpose</h2>
                <p class="text-xs text-gray-600 leading-relaxed">
                    To Let Koi is designed for the very Bangladesh to make a big and long-term impact. It offers a fast,
                    simple, and transparent rental platform that everyone can use. We believe in empowering owners and
                    tenants alike with technology. To Let Koi is a one-stop solution for all your rental needs - whether you
                    are looking for a flat, apartment, office, or shop. We are committed to building trust through verified
                    listings, secure payments, and excellent customer support.
                </p>
            </div>
        </section>

        {{-- 11. NEWS COVERAGE --}}
        <section class="py-6 bg-white border-t border-gray-100">
            <div class="max-w-6xl mx-auto px-4 text-center">
                <h2 class="text-xs font-bold text-dark mb-1">News Coverage</h2>
                <p class="text-[9px] text-gray-400 uppercase tracking-widest mb-4">Featured in leading media outlets</p>
                <div
                    class="flex flex-wrap justify-center gap-6 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                    @foreach (['Prothom Alo', 'The Daily Star', 'BD News 24', 'Jamuna TV', 'ATN News'] as $media)
                        <div class="text-sm font-bold text-gray-500">{{ $media }}</div>
                    @endforeach
                </div>
            </div>
        </section>

    </div>
@endsection
