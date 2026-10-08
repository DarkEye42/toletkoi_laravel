@extends('layouts.dashboard')

@section('title', 'Create Listing')
@section('header', 'Create New Listing')

@section('content')
<form action="{{ route('dashboard.listings.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-8">
    @csrf

    {{-- Basic Info --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Basic Information</h3>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"
                       placeholder="e.g. Modern 3BR Apartment in Dhanmondi">
            </div>
            <div class="sm:col-span-2">
                <label for="title_bn" class="block text-sm font-medium text-gray-700">Title (Bengali)</label>
                <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>

            <div>
                <label for="property_type_id" class="block text-sm font-medium text-gray-700">Property Type *</label>
                <select id="property_type_id" name="property_type_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Type</option>
                    @foreach($propertyTypes as $type)
                        <option value="{{ $type->id }}" {{ old('property_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="listing_type" class="block text-sm font-medium text-gray-700">Listing Type *</label>
                <select id="listing_type" name="listing_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="rent" {{ old('listing_type') === 'rent' ? 'selected' : '' }}>For Rent</option>
                    <option value="sale" {{ old('listing_type') === 'sale' ? 'selected' : '' }}>For Sale</option>
                    <option value="sublet" {{ old('listing_type') === 'sublet' ? 'selected' : '' }}>Sublet</option>
                    <option value="shared" {{ old('listing_type') === 'shared' ? 'selected' : '' }}>Shared</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Pricing</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price (৳) *</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="price_type" class="block text-sm font-medium text-gray-700">Price Type</label>
                <select id="price_type" name="price_type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                    <option value="one_time">One Time</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Property Details --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Property Details</h3>
        <div class="grid gap-4 sm:grid-cols-4">
            <div>
                <label for="bedrooms" class="block text-sm font-medium text-gray-700">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}" min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="bathrooms" class="block text-sm font-medium text-gray-700">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}" min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="balconies" class="block text-sm font-medium text-gray-700">Balconies</label>
                <input type="number" id="balconies" name="balconies" value="{{ old('balconies') }}" min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="area_sqft" class="block text-sm font-medium text-gray-700">Area (sqft)</label>
                <input type="number" id="area_sqft" name="area_sqft" value="{{ old('area_sqft') }}" min="0"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="floor_level" class="block text-sm font-medium text-gray-700">Floor Level</label>
                <input type="number" id="floor_level" name="floor_level" value="{{ old('floor_level') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="total_floors" class="block text-sm font-medium text-gray-700">Total Floors</label>
                <input type="number" id="total_floors" name="total_floors" value="{{ old('total_floors') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="furnishing_status" class="block text-sm font-medium text-gray-700">Furnishing</label>
                <select id="furnishing_status" name="furnishing_status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="unfurnished">Unfurnished</option>
                    <option value="semi_furnished" {{ old('furnishing_status') === 'semi_furnished' ? 'selected' : '' }}>Semi Furnished</option>
                    <option value="fully_furnished" {{ old('furnishing_status') === 'fully_furnished' ? 'selected' : '' }}>Fully Furnished</option>
                </select>
            </div>
            <div>
                <label for="available_from" class="block text-sm font-medium text-gray-700">Available From</label>
                <input type="date" id="available_from" name="available_from" value="{{ old('available_from') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>
    </div>

    {{-- Location --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Location</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700">Division *</label>
                <select id="division_id" name="division_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Division</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="district_id" class="block text-sm font-medium text-gray-700">District *</label>
                <select id="district_id" name="district_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select District</option>
                </select>
            </div>
            <div>
                <label for="area_id" class="block text-sm font-medium text-gray-700">Area</label>
                <select id="area_id" name="area_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select Area</option>
                </select>
            </div>
        </div>
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Full Address *</label>
            <input type="text" id="address" name="address" value="{{ old('address') }}" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20"
                   placeholder="House #, Road #, Block, Area">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="lat" class="block text-sm font-medium text-gray-700">Latitude</label>
                <input type="number" step="any" id="lat" name="lat" value="{{ old('lat') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="lng" class="block text-sm font-medium text-gray-700">Longitude</label>
                <input type="number" step="any" id="lng" name="lng" value="{{ old('lng') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Description</h3>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea id="description" name="description" rows="5" required
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">{{ old('description') }}</textarea>
        </div>
        <div>
            <label for="description_bn" class="block text-sm font-medium text-gray-700">Description (Bengali)</label>
            <textarea id="description_bn" name="description_bn" rows="4"
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">{{ old('description_bn') }}</textarea>
        </div>
    </div>

    {{-- Images --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Photos (Max 20)</h3>
        <input type="file" name="images[]" multiple accept="image/*"
               class="block w-full text-sm text-muted file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-emerald-100">
    </div>

    {{-- Contact Info --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <h3 class="text-lg font-semibold text-dark">Contact Information</h3>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name', Auth::user()->name) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="contact_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div>
                <label for="contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', Auth::user()->email) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.listings.index') }}" class="text-sm text-muted hover:text-gray-700">← Back to listings</a>
        <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition">
            Submit Listing for Review
        </button>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('division_id')?.addEventListener('change', async function() {
    const divisionId = this.value;
    const districtSelect = document.getElementById('district_id');
    if (!divisionId) { districtSelect.innerHTML = '<option value="">Select District</option>'; return; }
    const res = await fetch(`/api/areas?division_id=${divisionId}`);
    // Simplified: reload with division to populate districts
});
</script>
@endpush
@endsection
