@props(['listing', 'compact' => false])

@php
    $images = $listing->images?->count() ? $listing->images->map(fn($img) => Storage::disk('public')->url($img->path))->toArray() :
              [$listing->image_url ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80'];
    $image = $images[0];
    $location = $listing->area?->name ?? $listing->address ?? 'Dhaka';
    $location = $listing->area ? $location . ', ' . ($listing->area->district?->name ?? '') : $location;
    $price = number_format($listing->price);
    $url = $listing->getUrl();
@endphp

<a href="{{ $url }}" class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 group flex flex-col h-full overflow-hidden">
    <div class="relative h-32 overflow-hidden bg-gray-100">
        <img src="{{ $image }}" alt="{{ $listing->title }}" loading="lazy" decoding="async"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute bottom-2 right-2">
            <span class="bg-primary/90 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                ৳ {{ $price }}
            </span>
        </div>
    </div>
    <div class="p-2.5 flex flex-col flex-1">
        <h3 class="font-bold text-[11px] text-dark mb-1 leading-tight group-hover:text-primary transition-colors line-clamp-2">{{ $listing->title }}</h3>
        <p class="text-gray-400 text-[10px] mb-2 flex items-center gap-1 truncate">
            <i class="material-icons text-[10px]">place</i> {{ $location }}
        </p>
        <div class="flex items-center gap-2 mt-auto pt-2 border-t border-gray-50 text-[10px] text-gray-500 font-medium">
            @if($listing->bedrooms)
                <span>Room: {{ $listing->bedrooms }}</span>
            @endif
            @if($listing->bathrooms)
                <span class="text-gray-300">,</span>
                <span>Bath: {{ $listing->bathrooms }}</span>
            @endif
            @if($listing->area_sqft)
                <span class="text-gray-300">,</span>
                <span>{{ number_format($listing->area_sqft) }} sqft</span>
            @endif
        </div>
    </div>
</a>
