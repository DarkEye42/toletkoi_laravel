<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use App\Models\PropertyType;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService,
    ) {}

    /**
     * Show the search/listings page with filters.
     */
    public function index(SearchRequest $request)
    {
        $listings = $this->searchService->search($request)->paginate(12)->withQueryString();

        // For filter sidebar
        $divisions = Division::orderBy('name')->get();
        $propertyTypes = PropertyType::orderBy('name')->get();

        $districts = collect();
        if ($request->filled('division_id')) {
            $districts = District::where('division_id', $request->division_id)->orderBy('name')->get();
        }

        $areas = collect();
        if ($request->filled('district_id')) {
            $areas = Area::where('district_id', $request->district_id)->orderBy('name')->get();
        }

        return view('search.index', compact(
            'listings',
            'divisions',
            'districts',
            'areas',
            'propertyTypes'
        ));
    }

    /**
     * AJAX search API for live search.
     */
    public function apiSearch(SearchRequest $request)
    {
        $query = $this->searchService->search($request);

        if ($request->filled('bbox')) {
            $coords = explode(',', $request->input('bbox'));
            if (count($coords) === 4) {
                [$south, $west, $north, $east] = array_map('floatval', $coords);
                $query->whereBetween('lat', [$south, $north])
                      ->whereBetween('lng', [$west, $east]);
            }
        }

        $listings = $query->limit(50)->get()->map(function ($listing) {
            $listing->url = $listing->getUrl();
            $listing->primary_image_url = $listing->getPrimaryImageUrl();
            $listing->property_type_name = $listing->propertyType->name ?? '';
            return $listing;
        });

        return response()->json(['data' => $listings]);
    }

    /**
     * AJAX API for area lookup.
     */
    public function apiAreas(Request $request)
    {
        $query = Area::with('district');

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        return response()->json($query->limit(20)->get());
    }
}
