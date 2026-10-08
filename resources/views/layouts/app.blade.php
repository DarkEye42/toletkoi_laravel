<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <title>@yield('title', setting('seo.meta_title', 'ToLetKoi - Bangladesh\'s #1 Rental Platform'))</title>
    <meta name="description" content="@yield('description', setting('seo.meta_description', 'Find your perfect rental property in Bangladesh. Apartments, houses, offices for rent in Dhaka, Chattogram, and all divisions.'))">
    <meta name="keywords" content="@yield('keywords', 'rent apartment Bangladesh, property rental Dhaka, flat rent, house rent, office space')">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', 'ToLetKoi - Bangladesh\'s #1 Rental Platform')">
    <meta property="og:description" content="@yield('og_description', 'Find your perfect rental property in Bangladesh')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:locale" content="bn_BD">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Fonts: Material Icons + Plus Jakarta Sans + Noto Sans Bengali --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons|Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS via Vite (includes all styles: Tailwind, components, map CSS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet CSS for Map Search (Barikoi tiles) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <style>
        .barikoi-map .leaflet-control-attribution a { color: #00a1ff; }
    </style>

    {{-- Additional Styles --}}
    @stack('styles')

    {{-- Structured Data --}}
    @yield('structured_data')
</head>
<body class="h-full bg-gray-50 antialiased">

    {{-- Navigation --}}
    <x-layouts.nav />

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 max-w-sm" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
            <div class="rounded-lg bg-green-50 p-4 shadow-lg ring-1 ring-green-200">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 z-50 max-w-sm" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
            <div class="rounded-lg bg-red-50 p-4 shadow-lg ring-1 ring-red-200">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" /></svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">&times;</button>
                </div>
            </div>
        </div>
    @endif
    @php
        $categories = [
            ['name' => 'Family', 'slug' => 'family', 'icon' => 'family_restroom'],
            ['name' => 'Bachelor', 'slug' => 'bachelor', 'icon' => 'school'],
            ['name' => 'Office', 'slug' => 'office', 'icon' => 'business_center'],
            ['name' => 'Sublet', 'slug' => 'sublet', 'icon' => 'bedroom_parent'],
            ['name' => 'Hostel', 'slug' => 'hostel', 'icon' => 'hotel'],
            ['name' => 'Shop', 'slug' => 'shop', 'icon' => 'storefront'],
        ];
    @endphp
    {{-- Main Content --}}
    <main>
        {{-- ROW 3: Category Bar (Sticky) --}}
        <div class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm pb-2 pt-3">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-center gap-2 overflow-x-auto scrollbar-hide">
                    @foreach ($categories as $cat)
                        <a href="{{ route('search', ['type' => $cat['slug']]) }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-medium text-gray-600 hover:border-primary hover:text-primary hover:bg-primary/5 transition-all whitespace-nowrap bg-white">
                            <i class="material-icons text-[14px] opacity-70">{{ $cat['icon'] }}</i>
                            {{ $cat['name'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('search.map') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-medium text-gray-600 hover:border-primary hover:text-primary hover:bg-primary/5 transition-all whitespace-nowrap bg-white">
                        <i class="material-icons text-[14px] opacity-70">map</i>
                        Map View
                    </a>
                </div>
            </div>
        </div>
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-layouts.footer />

    {{-- Leaflet JS for Map Search (Barikoi tiles) --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @php
        // Built as a variable rather than inline @json([...]) because Blade's
        // directive parser cannot handle a nested array literal with "=>".
        $mapConfig = $barikoiConfig ?? [
            'configured' => false,
            'tile_url' => null,
            'center' => ['lat' => 23.8103, 'lng' => 90.4125],
        ];
    @endphp
    <script>
        // Server-rendered map configuration. A null tile_url means no Barikoi
        // key is configured, so the map views fall back to OpenStreetMap
        // tiles instead of failing to render.
        window.BARIKOI_CONFIG = @json($mapConfig);
    </script>

    {{-- Alpine.js Collapse Plugin (must load BEFORE Alpine core) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    {{-- Alpine.js for Interactive Components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Scripts --}}
    @stack('scripts')
</body>
</html>
