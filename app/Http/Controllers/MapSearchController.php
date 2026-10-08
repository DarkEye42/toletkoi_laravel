<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Listing;
use App\Models\PropertyType;

class MapSearchController extends Controller
{
    /**
     * Show the map-based search page.
     */
    public function index(SearchRequest $request)
    {
        $query = Listing::with(['propertyType', 'area.district.division'])
            ->where('status', 'active')
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        // Apply same filters as search
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $listings = $query->get();
        $propertyTypes = PropertyType::orderBy('name')->get();

        // Default center: Dhaka
        $centerLat = $request->get('lat', 23.8103);
        $centerLng = $request->get('lng', 90.4125);
        $zoom = $request->get('zoom', 12);

        return view('search.map', compact('listings', 'propertyTypes', 'centerLat', 'centerLng', 'zoom'));
    }
}
