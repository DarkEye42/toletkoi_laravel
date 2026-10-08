<?php

namespace App\Http\Controllers;

use App\Services\BarikoiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Server-side proxy for the Barikoi API.
 *
 * The API key stays on the server and is only exposed for tile rendering, so
 * clients can never spend the account's request quota directly.
 *
 * Every action returns a stable JSON shape and never leaks a raw provider
 * error to the browser.
 */
class BarikoiMapController extends Controller
{
    public function __construct(
        protected BarikoiService $barikoi
    ) {}

    /**
     * Map configuration for the front-end.
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'configured' => $this->barikoi->isConfigured(),
            'api_key' => $this->barikoi->getApiKey(),
            'tile_url' => $this->barikoi->getTileUrl(),
            'center' => BarikoiService::defaultCenter(),
        ]);
    }

    /**
     * Location autocomplete for the navbar search modal and map search.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        if (!$this->barikoi->isConfigured()) {
            return response()->json([
                'places' => [],
                'configured' => false,
                'message' => 'Location search is unavailable: no map API key is configured.',
            ]);
        }

        try {
            $places = $this->barikoi->autocomplete(
                $validated['q'],
                (int) ($validated['limit'] ?? 8)
            );
        } catch (\Throwable $e) {
            Log::error('Barikoi autocomplete endpoint failed.', ['error' => $e->getMessage()]);

            return response()->json([
                'places' => [],
                'error' => 'Location search is temporarily unavailable.',
            ], 502);
        }

        return response()->json([
            'places' => $places,
            'configured' => true,
        ]);
    }

    /**
     * Convert a free-text address into coordinates (Rupantor geocoding).
     */
    public function geocode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        if (!$this->barikoi->isConfigured()) {
            return response()->json([
                'configured' => false,
                'message' => 'Geocoding is unavailable: no map API key is configured.',
            ], 503);
        }

        try {
            $result = $this->barikoi->geocode($validated['address']);
        } catch (\Throwable $e) {
            Log::error('Barikoi geocode endpoint failed.', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Geocoding is temporarily unavailable.'], 502);
        }

        if ($result === null) {
            return response()->json(['error' => 'No results found for that address.'], 404);
        }

        return response()->json(['configured' => true, 'place' => $result]);
    }

    /**
     * Convert coordinates into an address.
     *
     * Accepts either `lat`/`lng` (Leaflet convention) or `latitude`/`longitude`.
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $latitude = $validated['lat'] ?? $validated['latitude'] ?? null;
        $longitude = $validated['lng'] ?? $validated['longitude'] ?? null;

        if ($latitude === null || $longitude === null) {
            return response()->json([
                'error' => 'Both a latitude and a longitude are required.',
            ], 422);
        }

        if (!$this->barikoi->isConfigured()) {
            return response()->json([
                'configured' => false,
                'message' => 'Reverse geocoding is unavailable: no map API key is configured.',
            ], 503);
        }

        try {
            // Barikoi takes longitude first.
            $result = $this->barikoi->reverseGeocode((float) $longitude, (float) $latitude);
        } catch (\Throwable $e) {
            Log::error('Barikoi reverse geocode endpoint failed.', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Reverse geocoding is temporarily unavailable.'], 502);
        }

        if ($result === null) {
            return response()->json(['error' => 'No address found for those coordinates.'], 404);
        }

        return response()->json(['configured' => true, 'place' => $result]);
    }
}
