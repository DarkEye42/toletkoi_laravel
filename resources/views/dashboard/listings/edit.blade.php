@extends('layouts.dashboard')
@section('title', 'Edit Listing')
@section('header', 'Edit: ' . Str::limit($listing->title, 40))

@section('content')
<form action="{{ route('dashboard.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-8">
    @csrf
    @method('PUT')

    {{-- Basic Info --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Basic Information</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Title *</label>
                <input type="text" name="title" value="{{ old('title', $listing->title) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Title (Bengali)</label>
                <input type="text" name="title_bn" value="{{ old('title_bn', $listing->title_bn) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Property Type *</label>
                <select name="property_type_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    @foreach($propertyTypes as $type)
                        <option value="{{ $type->id }}" {{ old('property_type_id', $listing->property_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Listing Type *</label>
                <select name="listing_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    @foreach(['rent' => 'For Rent', 'sale' => 'For Sale', 'sublet' => 'Sublet', 'shared' => 'Shared'] as $val => $label)
                        <option value="{{ $val }}" {{ old('listing_type', $listing->listing_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Pricing</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Price (৳) *</label>
                <input type="number" name="price" value="{{ old('price', $listing->price) }}" required min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Price Type</label>
                <select name="price_type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    @foreach(['monthly' => 'Monthly', 'yearly' => 'Yearly', 'one_time' => 'One Time'] as $val => $label)
                        <option value="{{ $val }}" {{ old('price_type', $listing->price_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Property Details --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Property Details</h3>
        <div class="grid gap-4 sm:grid-cols-4">
            <div><label class="block text-sm font-medium text-gray-700">Bedrooms</label><input type="number" name="bedrooms" value="{{ old('bedrooms', $listing->bedrooms) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Bathrooms</label><input type="number" name="bathrooms" value="{{ old('bathrooms', $listing->bathrooms) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Balconies</label><input type="number" name="balconies" value="{{ old('balconies', $listing->balconies) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Area (sqft)</label><input type="number" name="area_sqft" value="{{ old('area_sqft', $listing->area_sqft) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Floor Level</label><input type="number" name="floor_level" value="{{ old('floor_level', $listing->floor_level) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Total Floors</label><input type="number" name="total_floors" value="{{ old('total_floors', $listing->total_floors) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Furnishing</label>
                <select name="furnishing_status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    @foreach(['unfurnished' => 'Unfurnished', 'semi_furnished' => 'Semi Furnished', 'fully_furnished' => 'Fully Furnished'] as $val => $label)
                        <option value="{{ $val }}" {{ old('furnishing_status', $listing->furnishing_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700">Available From</label><input type="date" name="available_from" value="{{ old('available_from', $listing->available_from) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
        </div>
    </div>

    {{-- Location --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Location</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Division *</label>
                <select name="division_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id', $listing->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">District *</label>
                <select name="district_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->id }}" {{ old('district_id', $listing->district_id) == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Area</label>
                <select name="area_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $listing->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Address *</label>
            <input type="text" name="address" value="{{ old('address', $listing->address) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
        </div>
    </div>

    {{-- Description --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Description</h3>
        <div><label class="block text-sm font-medium text-gray-700">Description *</label><textarea name="description" rows="5" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">{{ old('description', $listing->description) }}</textarea></div>
        <div><label class="block text-sm font-medium text-gray-700">Description (Bengali)</label><textarea name="description_bn" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">{{ old('description_bn', $listing->description_bn) }}</textarea></div>
    </div>

    {{-- Existing Images --}}
    @if($listing->images->count())
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-4">
        <h3 class="text-lg font-semibold text-dark">Current Photos</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($listing->images as $image)
                <div class="relative h-24 w-24 rounded-lg overflow-hidden">
                    <img src="{{ Storage::disk('public')->url($image->path) }}" class="h-full w-full object-cover">
                    @if($image->is_primary)
                        <span class="absolute bottom-0 left-0 right-0 bg-primary/90 text-center text-xs text-white py-0.5">Primary</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Add More Images --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-4">
        <h3 class="text-lg font-semibold text-dark">Add More Photos</h3>
        <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-muted file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-emerald-100">
    </div>

    {{-- Contact --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Contact Information</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div><label class="block text-sm font-medium text-gray-700">Name</label><input type="text" name="contact_name" value="{{ old('contact_name', $listing->contact_name) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Phone</label><input type="text" name="contact_phone" value="{{ old('contact_phone', $listing->contact_phone) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
            <div><label class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="contact_email" value="{{ old('contact_email', $listing->contact_email) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"></div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.listings.index') }}" class="text-sm text-muted hover:text-gray-700">← Back</a>
        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition">Update Listing</button>
        </div>
    </div>
</form>
@endsection
