@extends('layouts.app')

@section('title', 'Search Properties - ToLetKoi')
@section('description', 'Search and filter rental properties across Bangladesh')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8" x-data="{ showFilters: false }">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-dark">Search Properties</h1>
        <p class="text-sm text-gray-400 mt-1">Find your perfect property from thousands of listings</p>
    </div>

    {{-- Mobile Filter Toggle --}}
    <button @click="showFilters = !showFilters" class="lg:hidden w-full mb-4 bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-dark flex items-center justify-between hover:border-primary/30 transition-colors">
        <span class="flex items-center gap-2">
            <i class="material-icons text-primary text-lg">tune</i> Filters
            @php
                $activeCount = collect(['listing_type','division_id','district_id','area_id','property_type_id','bedrooms','min_price','max_price'])->filter(fn($k) => request($k))->count();
            @endphp
            @if($activeCount)
                <span class="w-5 h-5 bg-primary text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $activeCount }}</span>
            @endif
        </span>
        <i class="material-icons text-gray-400" :class="showFilters ? 'rotate-180' : ''" x-cloak>expand_more</i>
    </button>

    <div class="flex gap-6">

        {{-- Filters Sidebar --}}
        <aside class="w-72 shrink-0" :class="showFilters ? 'block' : 'hidden lg:block'">
            <form action="{{ route('search') }}" method="GET" class="sticky top-24 bg-white border border-gray-100 rounded-2xl p-5 space-y-4">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-sm text-dark flex items-center gap-1.5">
                        <i class="material-icons text-primary text-lg">tune</i> Filters
                    </h3>
                    <a href="{{ route('search') }}" class="text-[10px] text-gray-400 hover:text-primary transition-colors">Clear all</a>
                </div>

                {{-- Listing Type --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Listing Type</label>
                    <select name="listing_type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">All Types</option>
                        <option value="rent" {{ request('listing_type') === 'rent' ? 'selected' : '' }}>For Rent</option>
                        <option value="sale" {{ request('listing_type') === 'sale' ? 'selected' : '' }}>For Sale</option>
                        <option value="sublet" {{ request('listing_type') === 'sublet' ? 'selected' : '' }}>Sublet</option>
                        <option value="shared" {{ request('listing_type') === 'shared' ? 'selected' : '' }}>Shared</option>
                    </select>
                </div>

                {{-- Division --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Division</label>
                    <select name="division_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- District --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">District</label>
                    <select name="district_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">All Districts</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ request('district_id') == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Area --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Area</label>
                    <select name="area_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">All Areas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Property Type --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Property Type</label>
                    <select name="property_type_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">All Types</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}" {{ request('property_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bedrooms --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Bedrooms</label>
                    <select name="bedrooms" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">Any</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                        @endfor
                    </select>
                </div>

                {{-- Bathrooms --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Bathrooms</label>
                    <select name="bathrooms" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">Any</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                        @endfor
                    </select>
                </div>

                {{-- Price Range --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Price Range (৳)</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>

                {{-- Area (sqft) --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Area (sqft)</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_area" value="{{ request('min_area') }}" placeholder="Min" class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <input type="number" name="max_area" value="{{ request('max_area') }}" placeholder="Max" class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>

                {{-- Furnishing --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Furnishing</label>
                    <select name="furnishing_status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <option value="">Any</option>
                        <option value="unfurnished" {{ request('furnishing_status') === 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                        <option value="semi_furnished" {{ request('furnishing_status') === 'semi_furnished' ? 'selected' : '' }}>Semi Furnished</option>
                        <option value="fully_furnished" {{ request('furnishing_status') === 'fully_furnished' ? 'selected' : '' }}>Fully Furnished</option>
                    </select>
                </div>

                {{-- Available From --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Available From</label>
                    <input type="date" name="available_from" value="{{ request('available_from') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>

                {{-- Verified Only --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="verified_only" value="1" {{ request('verified_only') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20">
                    <span class="text-xs font-medium text-gray-600">Verified Only</span>
                </label>

                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white px-5 py-3 rounded-xl text-sm font-bold shadow-md shadow-primary/20 flex items-center justify-center gap-2 transition-all hover:shadow-lg hover:shadow-primary/30">
                    <i class="material-icons text-[16px]">search</i> Search Properties
                </button>
            </form>
        </aside>

        {{-- Results --}}
        <div class="flex-1 min-w-0">
            {{-- Sort & Count --}}
            <div class="flex items-center justify-between mb-5 bg-white border border-gray-100 rounded-2xl px-5 py-3">
                <p class="text-xs text-gray-500">
                    <span class="font-bold text-dark text-sm">{{ $listings->total() }}</span> properties found
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('search.map') }}" class="hidden sm:flex items-center gap-1 text-[11px] font-semibold text-primary hover:text-primary-dark transition-colors px-3 py-1.5 bg-primary/5 rounded-lg">
                        <i class="material-icons text-[13px]">map</i> Map View
                    </a>
                    <div class="flex items-center gap-1.5">
                        <i class="material-icons text-gray-400 text-[14px]">sort</i>
                        @php $currentSort = request('sort', 'latest'); @endphp
                        <select onchange="window.location.href=this.value" class="bg-transparent border-none text-[11px] font-semibold text-gray-700 focus:outline-none cursor-pointer">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ $currentSort === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ $currentSort === 'price_low' ? 'selected' : '' }}>Price ↑</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ $currentSort === 'price_high' ? 'selected' : '' }}>Price ↓</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'area']) }}" {{ $currentSort === 'area' ? 'selected' : '' }}>Area</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}" {{ $currentSort === 'featured' ? 'selected' : '' }}>Featured</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Listings Grid --}}
            @if($listings->count())
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($listings as $listing)
                        <x-listings.card :listing="$listing" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $listings->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="material-icons text-gray-300 text-3xl">search_off</i>
                    </div>
                    <h3 class="text-lg font-bold text-dark">No properties found</h3>
                    <p class="text-sm text-gray-400 mt-1.5 max-w-sm mx-auto">Try adjusting your filters or search criteria to find more results.</p>
                    <a href="{{ route('search') }}" class="mt-5 inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl text-sm font-bold shadow-md shadow-primary/20 transition-all">
                        <i class="material-icons text-[16px]">refresh</i> Clear All Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
