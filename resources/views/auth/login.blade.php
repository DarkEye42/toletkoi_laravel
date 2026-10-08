@extends('layouts.guest')

@section('title', 'Login')
@section('heading', 'Welcome to ToLetKoi')
@section('subheading', 'Sign in to your account')

@section('content')
<div class="input-group">
    @error('email')
        <div class="mb-3 p-3 bg-danger-light border border-danger text-danger rounded-lg text-sm">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="input-group">
            <label class="label">Email Address</label>
            <input
                type="email"
                class="input"
                placeholder="admin@toletkoi.com"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            />
        </div>

        <div class="input-group">
            <label class="label">Password</label>
            <input
                type="password"
                class="input"
                placeholder="••••••••"
                name="password"
                required
            />
        </div>

        <div class="flex items-center justify-between mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-sm text-muted">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-full">
            Sign In
        </button>
    </form>
</div>
@endsection

@section('footer')
    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-muted">or sign in with</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-6">
        <a href="{{ route('social.redirect', 'google') }}" class="btn btn-light flex items-center justify-center gap-2">
            <i class="material-icons text-base">google</i>
            <span>Google</span>
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-light flex items-center justify-center gap-2">
            <i class="material-icons text-base">facebook</i>
            <span>Facebook</span>
        </a>
    </div>

    <p class="text-sm text-muted">
        New to ToLetKoi?
        <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Create an account</a>
    </p>

    <div class="mt-6 text-center text-sm text-muted">
        <p>&copy; 2026 ToLetKoi. All rights reserved.</p>
    </div>
@endsection
