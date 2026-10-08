<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchService
{
    /**
     * Build search query from request filters.
     */
    public function search(Request $request): Builder
    {
        $query = Listing::with([
            'propertyType',
            'propertySubtype',
            'division',
            'district',
            'area',
            'user',
            'images',
            'features',
        ])
        ->where('status', 'active');

        $this->applyTextFilter($query, $request);
        $this->applyCategoryFilters($query, $request);
        $this->applyGeographyFilters($query, $request);
        $this->applyBudgetFilter($query, $request);
        $this->applySizeFilters($query, $request);
        $this->applyFeatureFilters($query, $request);

        // Verified only
        if ($request->boolean('verified_only')) {
            $query->whereHas('user', fn($q) => $q->where('nid_verified_at', '!=', null));
        }

        return $this->applySorting($query, $request->input('sort', 'newest'));
    }

    /**
     * Free-text search across title, description and address.
     */
    protected function applyTextFilter(Builder $query, Request $request): void
    {
        if (!$request->filled('q')) {
            return;
        }

        $keyword = $request->input('q');

        $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%")
              ->orWhere('address', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * Listing type, property type, subtype, rooms and furnishing filters.
     */
    protected function applyCategoryFilters(Builder $query, Request $request): void
    {
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->input('listing_type'));
        }

        $this->applyAliasedFilter($query, $request, 'property_type_id', ['property_type', 'property_type_id']);

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->input('bedrooms'));
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', $request->input('bathrooms'));
        }

        if ($request->filled('subtype_id')) {
            $query->where('property_subtype_id', $request->input('subtype_id'));
        }

        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->input('furnishing_status'));
        }
    }

    /**
     * Division, district and area filters.
     */
    protected function applyGeographyFilters(Builder $query, Request $request): void
    {
        $this->applyAliasedFilter($query, $request, 'division_id', ['division', 'division_id']);
        $this->applyAliasedFilter($query, $request, 'district_id', ['district', 'district_id']);
        $this->applyAliasedFilter($query, $request, 'area_id', ['area', 'area_id']);
    }

    /**
     * Apply the first input from $keys that has a filled value.
     *
     * Supports the legacy short query keys alongside the newer *_id keys.
     */
    protected function applyAliasedFilter(Builder $query, Request $request, string $column, array $keys): void
    {
        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $query->where($column, $request->input($key));

                return;
            }
        }
    }

    /**
     * Budget range (supports budget_min/budget_max and min_price/max_price).
     *
     * Uses filled() rather than truthiness so an explicit 0 bound is honoured.
     */
    protected function applyBudgetFilter(Builder $query, Request $request): void
    {
        $this->applyAliasedRange($query, $request, 'price', '>=', ['budget_min', 'min_price']);
        $this->applyAliasedRange($query, $request, 'price', '<=', ['budget_max', 'max_price']);
    }

    /**
     * Floor area range (sqft).
     */
    protected function applySizeFilters(Builder $query, Request $request): void
    {
        if ($request->filled('min_area')) {
            $query->where('area_sqft', '>=', $request->input('min_area'));
        }

        if ($request->filled('max_area')) {
            $query->where('area_sqft', '<=', $request->input('max_area'));
        }

        if ($request->filled('available_from')) {
            $query->where('available_from', '<=', $request->input('available_from'));
        }
    }

    /**
     * Apply a lower/upper bound using the first filled key from $keys.
     */
    protected function applyAliasedRange(Builder $query, Request $request, string $column, string $operator, array $keys): void
    {
        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $query->where($column, $operator, $request->input($key));

                return;
            }
        }
    }

    /**
     * Boolean feature filters backed by the listing_features table.
     */
    protected function applyFeatureFilters(Builder $query, Request $request): void
    {
        $featureFilters = [
            'furnished'   => 'is_furnished',
            'parking'     => 'has_parking',
            'lift'        => 'has_lift',
            'cctv'        => 'has_cctv',
            'security'    => 'has_security',
            'gas'         => 'has_gas',
            'wifi'        => 'has_wifi',
            'ac'          => 'has_ac',
            'generator'   => 'has_generator',
        ];

        foreach ($featureFilters as $requestKey => $featureColumn) {
            if ($request->boolean($requestKey)) {
                $query->whereHas('features', function (Builder $fq) use ($featureColumn) {
                    $fq->where($featureColumn, true);
                });
            }
        }
    }

    /**
     * Apply sorting to the search query.
     */
    protected function applySorting(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'price_low'   => $query->orderBy('price', 'asc'),
            'price_high'  => $query->orderBy('price', 'desc'),
            'popular'     => $this->applyPopularSort($query),
            'newest'      => $query->orderByDesc('listings.created_at'),
            default       => $query->orderByDesc('listings.created_at'),
        };
    }

    /**
     * Order by most-viewed using a correlated subquery so the listings are not
     * duplicated when a listing has more than one stats row.
     */
    protected function applyPopularSort(Builder $query): Builder
    {
        return $query
            ->orderByDesc(
                \App\Models\ListingStat::select('view_count')
                    ->whereColumn('listing_stats.listing_id', 'listings.id')
                    ->orderByDesc('view_count')
                    ->limit(1)
            )
            ->orderByDesc('listings.created_at');
    }

    /**
     * Get featured listings for homepage.
     */
    public function getFeatured(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Listing::with(['propertyType', 'division', 'district', 'area', 'images'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent listings for homepage.
     */
    public function getRecent(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return Listing::with(['propertyType', 'division', 'district', 'area', 'images'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related listings (same area, different listing).
     */
    public function getRelated(Listing $listing, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return Listing::with(['propertyType', 'division', 'district', 'area', 'images'])
            ->where('status', 'active')
            ->where('id', '!=', $listing->id)
            ->where(function (Builder $q) use ($listing) {
                $q->where('area_id', $listing->area_id)
                  ->orWhere('district_id', $listing->district_id)
                  ->orWhere('property_type_id', $listing->property_type_id);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get listings by geographic region for SEO pages.
     */
    public function getByRegion(string $column, int $regionId, int $perPage = 12)
    {
        return Listing::with(['propertyType', 'division', 'district', 'area', 'images'])
            ->where('status', 'active')
            ->where($column, $regionId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get listings by property type for SEO pages.
     */
    public function getByPropertyType(int $typeId, int $perPage = 12)
    {
        return $this->getByRegion('property_type_id', $typeId, $perPage);
    }
}
