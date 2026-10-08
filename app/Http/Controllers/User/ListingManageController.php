<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListingRequest;
use App\Models\Listing;
use App\Models\PropertyType;
use App\Models\Division;
use App\Services\ListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingManageController extends Controller
{
    public function __construct(
        protected ListingService $listingService,
    ) {}

    /**
     * Show user's listings.
     */
    public function index(Request $request)
    {
        $query = Listing::where('user_id', Auth::id())->with('images');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->latest()->paginate(10);

        return view('dashboard.listings.index', compact('listings'));
    }

    /**
     * Show create listing form.
     */
    public function create()
    {
        $propertyTypes = PropertyType::with('subtypes')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('dashboard.listings.create', compact('propertyTypes', 'divisions'));
    }

    /**
     * Store a new listing.
     */
    public function store(ListingRequest $request)
    {
        $validated = $request->validated();

        // validated() returns scalar input only, so uploaded files must be
        // pulled from the request explicitly or the ListingService never
        // receives them and no images are ever stored.
        $validated['images'] = $this->uploadedImages($request);

        $this->listingService->createListing(Auth::user(), $validated);

        return redirect()->route('dashboard.listings.index')
            ->with('success', 'Listing created successfully! It will be visible after admin approval.');
    }

    /**
     * Collect the validated image uploads from the request.
     *
     * @return array<int, \Illuminate\Http\UploadedFile>
     */
    protected function uploadedImages(ListingRequest $request): array
    {
        $files = $request->file('images') ?? [];

        return array_values(array_filter(
            is_array($files) ? $files : [$files],
            fn ($file) => $file instanceof \Illuminate\Http\UploadedFile && $file->isValid()
        ));
    }

    /**
     * Show edit listing form.
     */
    public function edit(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $listing->load('images');
        $propertyTypes = PropertyType::with('subtypes')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $districts = $listing->division ? $listing->division->districts()->orderBy('name')->get() : collect();
        $areas = $listing->district ? $listing->district->areas()->orderBy('name')->get() : collect();

        return view('dashboard.listings.edit', compact('listing', 'propertyTypes', 'divisions', 'districts', 'areas'));
    }

    /**
     * Update a listing.
     */
    public function update(ListingRequest $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        // Files are not part of validated() output; merge them in so
        // ListingService::updateListing() can store new images.
        $validated['images'] = $this->uploadedImages($request);

        $this->listingService->updateListing($listing, $validated);

        return redirect()->route('dashboard.listings.index')
            ->with('success', 'Listing updated successfully!');
    }

    /**
     * Delete a listing.
     */
    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $this->listingService->deleteListing($listing);

        return redirect()->route('dashboard.listings.index')
            ->with('success', 'Listing deleted successfully!');
    }

    /**
     * Duplicate a listing.
     */
    public function duplicate(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $this->listingService->duplicateListing($listing, Auth::user());

        return redirect()->route('dashboard.listings.index')
            ->with('success', 'Listing duplicated as a new draft!');
    }
}
