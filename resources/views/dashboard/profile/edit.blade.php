@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('header', 'Profile Settings')

@section('content')
<div class="max-w-3xl space-y-8">

    {{-- Profile Form --}}
    <form action="{{ route('dashboard.profile.update') }}" method="POST" class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        @csrf
        @method('PUT')

        <h3 class="text-lg font-semibold text-dark">Personal Information</h3>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->profile?->phone) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>

            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->profile?->company_name) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>

        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">{{ old('bio', $user->profile?->bio) }}</textarea>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->profile?->address) }}"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
        </div>

        {{-- Location --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
                <select id="division_id" name="division_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Division</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id', $user->profile?->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="district_id" class="block text-sm font-medium text-gray-700">District</label>
                <select id="district_id" name="district_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select District</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->id }}" {{ old('district_id', $user->profile?->district_id) == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="area_id" class="block text-sm font-medium text-gray-700">Area</label>
                <select id="area_id" name="area_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $user->profile?->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Social Links --}}
        <h4 class="font-medium text-dark pt-4 border-t border-gray-100">Social Links</h4>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                <input type="url" id="website" name="website" value="{{ old('website', $user->profile?->website) }}" placeholder="https://"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook</label>
                <input type="url" id="facebook" name="facebook" value="{{ old('facebook', $user->profile?->facebook) }}" placeholder="https://facebook.com/..."
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter</label>
                <input type="url" id="twitter" name="twitter" value="{{ old('twitter', $user->profile?->twitter) }}" placeholder="https://twitter.com/..."
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin', $user->profile?->linkedin) }}" placeholder="https://linkedin.com/..."
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="rounded-lg bg-primary px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-primary-dark transition">
                Save Changes
            </button>
        </div>
    </form>

    {{-- Change Password --}}
    <form action="{{ route('dashboard.profile.update') }}" method="POST" class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        @csrf
        @method('PUT')

        <h3 class="text-lg font-semibold text-dark">Change Password</h3>

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <input type="password" id="current_password" name="current_password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="password" name="password"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="rounded-lg bg-gray-900 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-800 transition">
                Update Password
            </button>
        </div>
    </form>
</div>
@endsection
