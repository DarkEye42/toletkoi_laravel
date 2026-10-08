<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Show user's favorites.
     */
    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with(['listing.propertyType', 'listing.images', 'listing.area.district'])
            ->latest()
            ->paginate(12);

        return view('dashboard.favorites', compact('favorites'));
    }

    /**
     * Toggle favorite (AJAX).
     */
    public function toggle(Listing $listing)
    {
        $user = Auth::user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('listing_id', $listing->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'listing_id' => $listing->id,
            ]);
            $isFavorited = true;
        }

        if (request()->expectsJson()) {
            return response()->json([
                'is_favorited' => $isFavorited,
                'message' => $isFavorited ? 'Added to favorites' : 'Removed from favorites',
            ]);
        }

        return back()->with('success', $isFavorited ? 'Added to favorites' : 'Removed from favorites');
    }

    /**
     * Remove from favorites.
     */
    public function destroy(Listing $listing)
    {
        Favorite::where('user_id', Auth::id())
            ->where('listing_id', $listing->id)
            ->delete();

        return back()->with('success', 'Removed from favorites');
    }
}
