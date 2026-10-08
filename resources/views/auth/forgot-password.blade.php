@extends('layouts.guest')

@section('title', 'Forgot Password')
@section('heading', 'Reset your password')

@section('content')
<p class="text-sm text-muted mb-4">Enter your email and we'll send you a link to reset your password.</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition">
        Send Reset Link
    </button>
</form>
@endsection

@section('footer')
<p>Remember your password? <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary">Login here</a></p>
@endsection
