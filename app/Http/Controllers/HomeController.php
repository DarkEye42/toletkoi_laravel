<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Division;
use App\Models\District;
use App\Models\Listing;
use App\Models\PropertyType;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the homepage.
     */
    public function index()
    {
        $featuredListings = Listing::with(['propertyType', 'area.district.division', 'images'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->where('featured_until', '>=', now())
            ->limit(8)
            ->latest()
            ->get();

        $recentListings = Listing::with(['propertyType', 'area.district.division', 'images'])
            ->where('status', 'active')
            ->limit(12)
            ->latest()
            ->get();

        $propertyTypes = PropertyType::withCount('listings')
            ->whereHas('listings', fn($q) => $q->where('status', 'active'))
            ->get();

        $divisions = Division::withCount('listings')
            ->whereHas('listings', fn($q) => $q->where('status', 'active'))
            ->get();

        $popularAreas = Area::where('is_popular', true)
            ->orWhere('property_count', '>', 0)
            ->orderByDesc('property_count')
            ->limit(16)
            ->get();

        $recentArticles = BlogPost::published()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $stats = [
            'total_listings' => Listing::where('status', 'active')->count(),
            'total_divisions' => Division::count(),
            'total_areas' => Area::count(),
        ];

        return view('home', compact(
            'featuredListings',
            'recentListings',
            'propertyTypes',
            'divisions',
            'popularAreas',
            'recentArticles',
            'stats'
        ));
    }

    /**
     * Show listings for a division.
     */
    public function division(Division $division)
    {
        $districts = $division->districts()->withCount('listings')->get();

        $listings = Listing::with(['propertyType', 'area.district', 'images'])
            ->where('status', 'active')
            ->where('division_id', $division->id)
            ->latest()
            ->paginate(12);

        return view('pages.division', compact('division', 'districts', 'listings'));
    }

    /**
     * Show listings for a district.
     */
    public function district(Division $division, District $district)
    {
        $areas = $district->areas()->withCount('listings')->get();

        $listings = Listing::with(['propertyType', 'area', 'images'])
            ->where('status', 'active')
            ->where('district_id', $district->id)
            ->latest()
            ->paginate(12);

        return view('pages.district', compact('division', 'district', 'areas', 'listings'));
    }

    /**
     * Show listings for an area.
     */
    public function area(Division $division, District $district, Area $area)
    {
        $listings = Listing::with(['propertyType', 'images'])
            ->where('status', 'active')
            ->where('area_id', $area->id)
            ->latest()
            ->paginate(12);

        return view('pages.area', compact('division', 'district', 'area', 'listings'));
    }

    /**
     * Show listings by property type.
     */
    public function propertyType(PropertyType $propertyType)
    {
        $subtypes = $propertyType->subtypes()->withCount('listings')->get();

        $listings = Listing::with(['area.district.division', 'images'])
            ->where('status', 'active')
            ->where('property_type_id', $propertyType->id)
            ->latest()
            ->paginate(12);

        return view('pages.property-type', compact('propertyType', 'subtypes', 'listings'));
    }

    /**
     * AJAX endpoint for location search.
     */
    public function searchLocations(Request $request)
    {
        $query = $request->input('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $areas = Area::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'name_bn', 'district_id'])
            ->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'name_bn' => $a->name_bn,
                'type' => 'area',
                'district_name' => $a->district?->name,
            ]);

        $districts = District::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'division_id'])
            ->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'type' => 'district',
            ]);

        $divisions = Division::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'type' => 'division',
            ]);

        return response()->json($areas->merge($districts)->merge($divisions)->values());
    }
}
