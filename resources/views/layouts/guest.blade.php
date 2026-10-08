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
    <title>@yield('title', 'ToLetKoi') - Authentication</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-light antialiased font-sans">
    <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">

            {{-- Logo & Welcome --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4">
                    <span class="text-3xl font-bold text-white">T</span>
                </div>
                <h2 class="text-3xl font-bold text-dark mb-2">@yield('heading', 'Welcome!')</h2>
                <p class="text-muted">@yield('subheading', 'Sign in to your account')</p>
            </div>

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="mb-4 p-3 bg-info-light border border-info text-info rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('success'))
                <div class="mb-4 p-3 bg-success-light border border-success text-success rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card shadow-material-lg">
                <div class="card-body">
                    @yield('content')
                </div>
            </div>

            {{-- Footer Links --}}
            <div class="text-center text-sm text-muted mt-6">
                @yield('footer')
            </div>
        </div>
    </div>
</body>
</html>
