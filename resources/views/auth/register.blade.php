@extends('layouts.guest')

@section('title', 'Register')
@section('heading', 'Join ToLetKoi')
@section('subheading', 'Create your account to start listing')

@section('content')
<div class="input-group">
    @error('email')
        <div class="mb-3 p-3 bg-danger-light border border-danger text-danger rounded-lg text-sm">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="input-group">
            <label class="label">Full Name</label>
            <input
                type="text"
                class="input"
                placeholder="John Doe"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
            />
            @error('name') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="input-group">
            <label class="label">Email Address</label>
            <input
                type="email"
                class="input"
                placeholder="john@example.com"
                name="email"
                value="{{ old('email') }}"
                required
            />
            @error('email') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="input-group">
            <label class="label">Phone Number</label>
            <input
                type="tel"
                class="input"
                placeholder="017..."
                name="phone"
                value="{{ old('phone') }}"
                required
            />
            @error('phone') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="input-group">
            <label class="label">I am a...</label>
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['value' => 'landlord', 'label' => 'Landlord', 'icon' => 'home'],
                    ['value' => 'tenant', 'label' => 'Tenant', 'icon' => 'person'],
                    ['value' => 'agent', 'label' => 'Agent', 'icon' => 'volunteer_activism'],
                ] as $roleOpt)
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="role"
                            value="{{ $roleOpt['value'] }}"
                            {{ old('role') === $roleOpt['value'] ? 'checked' : '' }}
                            class="peer sr-only"
                        >
                        <div class="rounded-lg border-2 border-gray-200 p-3 text-center transition hover:border-primary peer-checked:border-primary peer-checked:bg-primary/5">
                            <div class="text-2xl"><i class="material-icons text-primary">{{ $roleOpt['icon'] }}</i></div>
                            <div class="mt-1 text-sm font-medium text-gray-700">{{ $roleOpt['label'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('role') <p class="error-message mt-2">{{ $message }}</p> @enderror
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
            @error('password') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="input-group">
            <label class="label">Confirm Password</label>
            <input
                type="password"
                class="input"
                placeholder="••••••••"
                name="password_confirmation"
                required
            />
        </div>

        <button type="submit" class="btn btn-primary w-full">
            Register
        </button>

        <p class="mt-4 text-center text-xs text-muted">
            By registering, you agree to our
            <a href="{{ route('pages.show', 'terms') }}" class="text-primary hover:underline">Terms of Service</a>
            and
            <a href="{{ route('pages.show', 'privacy') }}" class="text-primary hover:underline">Privacy Policy</a>.
        </p>
    </form>
</div>
@endsection

@section('footer')
    <p class="text-sm text-muted">
        Already have an account?
        <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Sign In</a>
    </p>

    <div class="mt-6 text-center text-sm text-muted">
        <p>&copy; 2026 ToLetKoi. All rights reserved.</p>
    </div>
@endsection
