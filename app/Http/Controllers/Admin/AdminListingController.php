<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\PropertyType;
use App\Notifications\ListingApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminListingController extends Controller
{
    /**
     * Paginated listing queue with status and search filters.
     */
    public function index(Request $request)
    {
        $listings = Listing::withoutGlobalScopes()
            ->with(['user', 'propertyType', 'division', 'district', 'area', 'images'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('property_type_id'), fn($q) => $q->where('property_type_id', $request->input('property_type_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->input('q');
                $q->where(fn($sub) => $sub
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%"));
            })
            ->when($request->input('sort') === 'oldest', fn($q) => $q->oldest(), fn($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        $propertyTypes = PropertyType::orderBy('name')->get();

        $counts = [
            'all' => Listing::withoutGlobalScopes()->count(),
            'pending' => Listing::withoutGlobalScopes()->where('status', 'pending')->count(),
            'active' => Listing::withoutGlobalScopes()->where('status', 'active')->count(),
            'rejected' => Listing::withoutGlobalScopes()->where('status', 'rejected')->count(),
        ];

        return view('admin.listings.index', compact('listings', 'propertyTypes', 'counts'));
    }

    /**
     * Full listing detail for moderation review.
     */
    public function show(Listing $listing)
    {
        $listing->load(['user', 'propertyType', 'propertySubtype', 'division', 'district', 'area', 'images', 'features', 'stats']);

        return view('admin.listings.show', compact('listing'));
    }

    /**
     * Approve a listing so it becomes publicly visible.
     */
    public function approve(Listing $listing)
    {
        $listing->update([
            'status' => 'active',
            'rejection_reason' => null,
        ]);

        try {
            $listing->user->notify(new ListingApprovedNotification($listing));
        } catch (\Throwable $e) {
            // Notification delivery must never block moderation.
            Log::warning('Listing approval notification failed.', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', "Listing \"{$listing->title}\" approved and published.");
    }

    /**
     * Reject a listing with a reason shown to the owner.
     */
    public function reject(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $listing->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Listing \"{$listing->title}\" rejected.");
    }

    /**
     * Toggle the featured flag used for homepage promotion.
     */
    public function toggleFeatured(Listing $listing)
    {
        $featured = !$listing->is_featured;

        $listing->update([
            'is_featured' => $featured,
            'featured_until' => $featured ? now()->addDays(30) : null,
        ]);

        return back()->with(
            'success',
            $featured
                ? "Listing \"{$listing->title}\" is now featured for 30 days."
                : "Listing \"{$listing->title}\" removed from featured."
        );
    }

    /**
     * Permanently delete a listing and its stored files.
     */
    public function destroy(Listing $listing)
    {
        $title = $listing->title;

        app(\App\Services\ListingService::class)->deleteListing($listing);

        return redirect()->route('admin.listings.index')
            ->with('success', "Listing \"{$title}\" deleted.");
    }
}
