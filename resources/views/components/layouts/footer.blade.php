{{-- resources/views/components/layouts/footer.blade.php --}}
@php
    $footerLinks = [
        ['name' => 'About Us', 'href' => '/about'],
        ['name' => 'Our Team', 'href' => '#'],
        ['name' => 'News Coverage', 'href' => '#'],
        ['name' => 'Affiliate Program', 'href' => '#'],
        ['name' => 'FAQ', 'href' => route('faq')],
        ['name' => 'Refund Policy', 'href' => '#'],
        ['name' => 'Privacy Policy', 'href' => '#'],
        ['name' => 'Terms & Condition', 'href' => '#'],
        ['name' => 'Disclaimer', 'href' => '#'],
        ['name' => 'Contact Us', 'href' => route('contact')],
    ];

    $adLabels = ['Rent Apartments', 'Find Rooms', 'Verified Listings', 'Best Deals'];

    $popularAreasFooter = \App\Models\Area::where('property_count', '>', 0)
        ->orderByDesc('property_count')
        ->limit(16)
        ->get();
@endphp

<footer class="bg-gray-50 border-t border-gray-200 text-dark text-xs py-8 font-medium">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Popular Pages - Text Links 4-Column Grid --}}
        @if($popularAreasFooter->count())
            <div class="mb-8">
                <div class="text-center mb-4">
                    <h3 class="text-base font-bold text-dark">Popular Pages</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Explore top rental locations & latest ads in Bangladesh</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 md:p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3">
                        @foreach($popularAreasFooter as $idx => $area)
                            @php
                                $label = $area->name_bn ?? $area->name;
                                $sub = $area->district?->name_bn ?? $area->district?->name ?? '';
                                $href = route('area.show', [
                                    'division' => $area->district->division->slug ?? $area->district->division->id,
                                    'district' => $area->district->slug ?? $area->district->id,
                                    'area' => $area->slug ?? $area->id,
                                ]);
                                $adLabel = $adLabels[$idx % count($adLabels)];
                            @endphp
                            <a href="{{ $href }}" class="group" title="{{ $label }}">
                                <span class="text-[11px] font-semibold block truncate text-dark group-hover:text-primary group-hover:underline transition-colors">{{ $label }}</span>
                                <span class="text-[9px] text-gray-400 block truncate group-hover:text-primary/70 transition-colors">{{ $sub }} &middot; {{ $adLabel }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Payment Partners --}}
        <div class="border-t border-gray-200 pt-6 mb-6">
            <div class="text-center mb-4">
                <h3 class="text-base font-bold text-dark">Payment Partner</h3>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4 md:p-6">
                <img src="{{ asset('images/payments_partners.png') }}" alt="Payment Partners" class="w-full h-auto" loading="lazy"
                     onerror="this.src='https://via.placeholder.com/800x100?text=Payment+Partners'">
            </div>
        </div>

        {{-- Bottom Links --}}
        <div class="border-t border-gray-200 pt-4 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400">
            <div class="flex flex-wrap justify-center gap-x-3 gap-y-1">
                @foreach($footerLinks as $link)
                    <a href="{{ $link['href'] }}" class="hover:text-primary transition-colors">{{ $link['name'] }}</a>
                @endforeach
            </div>
            <p class="mt-2 md:mt-0">&copy; {{ date('Y') }} ToLetKoi - Bangladesh&apos;s Trusted House Rental Platform. All rights reserved.</p>
        </div>
    </div>
</footer>
