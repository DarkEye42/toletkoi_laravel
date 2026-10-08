<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientCreditException;
use App\Models\ContactUnlock;
use App\Models\District;
use App\Models\Listing;
use App\Models\ListingView;
use App\Notifications\ContactUnlockedNotification;
use App\Services\CreditService;
use App\Services\ListingService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct(
        protected SearchService $searchService,
        protected CreditService $creditService,
        protected ListingService $listingService,
    ) {}

    /**
     * Show all listings.
     */
    public function index()
    {
        $listings = $this->searchService->search(request())->paginate(12);

        return view('listings.index', compact('listings'));
    }

    /**
     * Show a single listing detail page.
     *
     * URL: /listing/{district:slug}/{area:slug}/{propertyType:slug}/{propertySubtype:slug}/{listing:slug}
     */
    public function show(District $district, $area, $propertyType, $propertySubtype, Listing $listing)
    {
        if ($listing->status !== 'active') {
            abort(404);
        }

        // Eager-load relationships needed for display and validation
        $listing->load([
            'propertyType',
            'propertySubtype',
            'area.district.division',
            'images',
            'features',
            'stats',
            'user.profile',
        ]);

        // Validate that the URL slugs match the listing's actual relationships.
        // This prevents access via stale/incorrect slugs while keeping SEO-friendly URLs.
        if (
            $listing->district->slug !== $district->slug ||
            $listing->area->slug !== $area ||
            $listing->propertyType->slug !== $propertyType ||
            $listing->propertySubtype->slug !== $propertySubtype
        ) {
            abort(404);
        }

        $this->listingService->recordView($listing);

        // Track view
        ListingView::create([
            'listing_id' => $listing->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $contactUnlocked = false;
        if (Auth::check()) {
            $contactUnlocked = ContactUnlock::where('user_id', Auth::id())
                ->where('lead_id', $listing->id)
                ->exists();
        }

        $relatedListings = $this->searchService->getRelated($listing);

        return view('listings.show', compact('listing', 'contactUnlocked', 'relatedListings'));
    }

    /**
     * Unlock contact information for a listing.
     * AJAX endpoint - requires auth + verification.
     */
    public function unlockContact(Request $request, Listing $listing)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(!$user, 401, 'Unauthenticated.');

        // Check if already unlocked
        $alreadyUnlocked = ContactUnlock::where('user_id', $user->id)
                ->where('lead_id', $listing->id)
                ->exists();

        if ($alreadyUnlocked) {
            return response()->json([
                'success' => true,
                'message' => 'Contact already unlocked.',
                'contact' => [
                    'name' => $listing->contact_name,
                    'phone' => $listing->contact_phone,
                    'email' => $listing->contact_email,
                ],
            ]);
        }

        try {
            $this->creditService->deductForUnlock($user, $listing);
        } catch (InsufficientCreditException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'credits_needed' => $e->creditsNeeded,
                'credits_available' => $e->creditsAvailable,
            ], 402);
        }

        // Send notification with contact details
        $user->notify(new ContactUnlockedNotification($user, $listing, $this->creditService->getUnlockCost()));

        return response()->json([
            'success' => true,
            'message' => 'Contact information unlocked successfully.',
            'contact' => [
                'name' => $listing->contact_name,
                'phone' => $listing->contact_phone,
                'email' => $listing->contact_email,
            ],
        ]);
    }
}
