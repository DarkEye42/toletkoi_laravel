@php
    $adminNav = [
        [
            'title' => 'Overview',
            'items' => [['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard']],
        ],
        [
            'title' => 'Moderation',
            'items' => [['route' => 'admin.listings.index', 'label' => 'Listings', 'icon' => 'apartment']],
        ],
    ];

    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ToLetKoi Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
</head>

<body class="h-full bg-gray-50 antialiased font-sans" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false">
    </div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 overflow-y-auto transition-transform duration-300 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-full flex-col">
            {{-- Brand --}}
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="ToLetKoi" class="h-8 w-auto">
                    <span
                        class="text-[10px] font-bold text-primary uppercase tracking-wider border-primary/30 rounded px-1.5 py-0.5">Admin</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-dark">
                    <i class="material-icons">close</i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 customized-scrollbar">
                @foreach ($adminNav as $section)
                    <div class="mb-6">
                        <h6 class="px-6 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            {{ $section['title'] }}</h6>
                        <ul>
                            @foreach ($section['items'] as $item)
                                @php
                                    $active =
                                        request()->routeIs($item['route']) ||
                                        request()->routeIs(str_replace('.index', '.*', $item['route']));
                                    $href = Route::has($item['route']) ? route($item['route']) : '#';
                                @endphp
                                <li>
                                    <a href="{{ $href }}"
                                        class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors
                                           {{ $active ? 'text-primary bg-primary/5 border-r-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                        <i
                                            class="material-icons text-lg {{ $active ? 'text-primary' : 'text-gray-400' }}">{{ $item['icon'] }}</i>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="px-6 pt-2">
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-2 text-xs font-medium text-gray-400 hover:text-primary transition-colors">
                        <i class="material-icons text-[16px]">open_in_new</i> View public site
                    </a>
                </div>
            </nav>

            {{-- User + logout --}}
            <div class="shrink-0 p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div
                        class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold uppercase shrink-0">
                        {{ substr($user->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-dark truncate mb-0">{{ $user->name ?? 'Admin' }}</p>
                        <p class="text-[10px] text-muted truncate mb-0">
                            {{ $user?->getPrimaryRole()?->name ?? 'Admin' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-gray-50 hover:bg-danger-light text-gray-500 hover:text-danger px-4 py-2.5 text-xs font-bold transition-colors">
                        <i class="material-icons text-[16px]">logout</i> Sign out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Content --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 h-16 bg-white border-b border-gray-200 z-30 flex items-center gap-3 px-4 sm:px-6">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-dark">
                <i class="material-icons text-xl">menu</i>
            </button>
            <h1 class="text-base font-bold text-dark truncate">@yield('header', 'Admin')</h1>
            <div class="ml-auto flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash --}}
        @if (session('success'))
            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="flex items-start gap-3 rounded-xl bg-success-light border-success/30 p-4">
                    <i class="material-icons text-success text-lg shrink-0">check_circle</i>
                    <p class="text-sm font-medium text-dark mb-0">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="flex items-start gap-3 rounded-xl bg-danger-light border-danger/30 p-4">
                    <i class="material-icons text-danger text-lg shrink-0">error_outline</i>
                    <p class="text-sm font-medium text-dark mb-0">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <main class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
