<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureListingOwner
{
    /**
     * Ensure the authenticated user owns the listing.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $listing = $request->route('listing');

        if (!$listing || $listing->user_id !== $request->user()->id) {
            abort(403, 'You do not have permission to manage this listing.');
        }

        return $next($request);
    }
}
