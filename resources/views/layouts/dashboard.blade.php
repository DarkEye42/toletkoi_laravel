<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Google Fonts: Material Icons + Plus Jakarta Sans + Noto Sans Bengali --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons|Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <title>@yield('title', 'Dashboard') - ToLetKoi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gray-50 antialiased font-sans" x-data="{ sidebarOpen: false }">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" x-transition style="display: none;"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 overflow-y-auto transition-transform duration-300 ease-in-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-full flex-col">
            {{-- Logo --}}
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="ToLetKoi" class="h-8 w-auto">
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="material-icons">close</i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="overflow-y-auto py-4 h-[calc(100%-9rem)] customized-scrollbar">
                @php
                    $isOwner = auth()->user()?->hasRole('Landlord') || auth()->user()?->hasRole('Agent');
                    $navSections = [
                        [
                            'title' => 'My Account',
                            'items' => [
                                ['route' => 'dashboard.index', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                                ['route' => 'dashboard.profile.edit', 'label' => 'Profile', 'icon' => 'person'],
                                ['route' => 'dashboard.credits.index', 'label' => 'My Credits', 'icon' => 'payments'],
                                ['route' => 'dashboard.subscription.index', 'label' => 'Subscription', 'icon' => 'subscriptions'],
                            ],
                        ],
                    ];

                    if ($isOwner) {
                        $navSections[0]['items'][] = ['route' => 'dashboard.favorites.index', 'label' => 'Saved Properties', 'icon' => 'favorite'];
                        $navSections[0]['items'][] = ['route' => 'dashboard.listings.index', 'label' => 'My Listings', 'icon' => 'list_alt'];
                        $navSections[0]['items'][] = ['route' => 'dashboard.saved-searches.index', 'label' => 'Saved Searches', 'icon' => 'saved_search'];
                    } else {
                        $navSections[0]['items'][] = ['route' => 'dashboard.favorites.index', 'label' => 'Saved Properties', 'icon' => 'favorite'];
                        $navSections[0]['items'][] = ['route' => 'dashboard.saved-searches.index', 'label' => 'Saved Searches', 'icon' => 'saved_search'];
                    }

                    $navSections[] = [
                        'title' => 'Support',
                        'items' => [
                            ['route' => '#', 'label' => 'Help', 'icon' => 'help'],
                        ],
                    ];
                @endphp

                @foreach($navSections as $section)
                    <div class="mb-6">
                        <h6 class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ $section['title'] }}</h6>
                        <ul>
                            @foreach($section['items'] as $item)
                                @php
                                    $active = request()->routeIs($item['route'] ?? '') || request()->is($item['route'] ?? '');
                                    $href = $item['route'] === '#' ? '#' : route($item['route']);
                                @endphp
                                <li>
                                    <a href="{{ $href }}"
                                       class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors
                                           {{ $active ? 'text-primary bg-blue-50 border-r-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                        <i class="material-icons text-lg {{ $active ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }}">{{ $item['icon'] }}</i>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            {{-- User Info --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" onclick="document.getElementById('logout-form').submit();">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-semibold shadow-md uppercase">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->getPrimaryRole() }}</p>
                    </div>
                    <i class="material-icons text-gray-400">logout</i>
                </div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 right-0 left-0 lg:left-64 h-16 bg-white border-b border-gray-200 z-30 flex items-center justify-between px-6">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                <i class="material-icons text-xl">menu</i>
            </button>
            <h1 class="text-lg font-semibold text-gray-900">@yield('header', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-4">
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    {{-- Mobile Sidebar FAB Toggle --}}
    <button
        @click="sidebarOpen = true"
        class="fixed bottom-4 right-4 lg:hidden w-12 h-12 bg-primary text-white rounded-full shadow-lg z-50 flex items-center justify-center hover:bg-primary-dark transition-colors"
        :class="sidebarOpen ? 'hidden' : 'flex'"
        style="display: none;"
        x-show="!sidebarOpen"
    >
        <i class="material-icons">menu</i>
    </button>

    @stack('scripts')
</body>
</html>
