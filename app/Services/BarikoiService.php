<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BarikoiService
{
    /**
     * Barikoi API base host (no trailing /v2/api).
     *
     * The official Barikoi Laravel package uses https://barikoi.xyz as the
     * base URL and appends /v2/api/... in each endpoint path.
     */
    protected string $baseUrl = 'https://barikoi.xyz';

    protected ?string $apiKey;

    protected bool $configured = false;

    public function __construct()
    {
        $key = config('services.barikoi.key');
        $this->apiKey = is_string($key) && $key !== '' ? $key : null;
        $this->configured = $this->apiKey !== null;
    }

    /**
     * True when an API key is available.
     */
    public function isConfigured(): bool
    {
        return $this->configured;
    }

    /**
     * Get API key for client-side use (tile rendering).
     */
    public function getApiKey(): string
    {
        return $this->apiKey ?? '';
    }

    /**
     * Get the tile URL for Leaflet maps.
     *
     * Barikoi has deprecated raster tiles and migrated to their own GL JS
     * library (bkoi-gl). Since this project uses Leaflet, we return null
     * so views gracefully fall back to OpenStreetMap tiles.
     *
     * To use Barikoi tiles: https://docs.barikoi.com/docs/maps-api
     */
    public function getTileUrl(): ?string
    {
        return null;
    }

    /**
     * Autocomplete search for places.
     *
     * Endpoint: GET /v2/api/search/autocomplete/place
     * Docs: https://docs.barikoi.com/laravel/docs/location-api#autocomplete
     *
     * @return array<int, array<string, mixed>>
     */
    public function autocomplete(string $query, int $limit = 8): array
    {
        $query = trim($query);

        if (!$this->configured || mb_strlen($query) < 2) {
            return [];
        }

        $cacheKey = 'barikoi_autocomplete_' . md5($query . '|' . $limit);

        return Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            try {
                $response = Http::timeout(10)->acceptJson()->retry(2, 200)
                    ->get($this->baseUrl . '/v2/api/search/autocomplete/place', [
                        'api_key' => $this->apiKey,
                        'q' => $query,
                    ]);

                if (!$response->successful()) {
                    Log::warning('Barikoi autocomplete request failed.', [
                        'status' => $response->status(),
                        'query' => $query,
                    ]);

                    return [];
                }

                $places = $response->json('places');

                if (!is_array($places)) {
                    return [];
                }

                return collect($places)
                    ->take($limit)
                    ->map(fn ($place) => $this->normalizePlace((array) $place))
                    ->values()
                    ->all();
            } catch (\Throwable $e) {
                Log::error('Barikoi autocomplete error: ' . $e->getMessage());

                return [];
            }
        });
    }

    /**
     * Geocode an address string to coordinates (Rupantor engine).
     *
     * Endpoint: POST /v2/api/search/rupantor/geocode
     * Docs: https://docs.barikoi.com/laravel/docs/location-api#geocoding-rupantor
     *
     * Returns the first match from the geocoded results.
     *
     * @return array<string, mixed>|null
     */
    public function geocode(string $address): ?array
    {
        $address = trim($address);

        if (!$this->configured || $address === '') {
            return null;
        }

        $cacheKey = 'barikoi_geocode_' . md5($address);

        return Cache::remember($cacheKey, 600, function () use ($address) {
            try {
                $response = Http::timeout(10)->acceptJson()->retry(2, 200)
                    ->asForm()
                    ->post($this->baseUrl . '/v2/api/search/rupantor/geocode', [
                        'api_key' => $this->apiKey,
                        'q' => $address,
                    ]);

                if (!$response->successful()) {
                    Log::warning('Barikoi geocode request failed.', [
                        'status' => $response->status(),
                        'address' => $address,
                    ]);

                    return null;
                }

                $data = $response->json();

                if (!is_array($data) || !isset($data['geocoded_address'])) {
                    return null;
                }

                $geo = $data['geocoded_address'];

                return [
                    'id' => null,
                    'address' => $geo['address'] ?? $address,
                    'area' => $geo['area'] ?? null,
                    'city' => $geo['city'] ?? null,
                    'district' => $geo['district'] ?? null,
                    'division' => null,
                    'post_code' => $geo['postcode'] ?? null,
                    'place_type' => null,
                    'longitude' => isset($geo['longitude']) ? (float) $geo['longitude'] : null,
                    'latitude' => isset($geo['latitude']) ? (float) $geo['latitude'] : null,
                ];
            } catch (\Throwable $e) {
                Log::error('Barikoi geocode error: ' . $e->getMessage());

                return null;
            }
        });
    }

    /**
     * Reverse geocode coordinates to an address.
     *
     * Endpoint: GET /v2/api/search/reverse/geocode
     * Docs: https://docs.barikoi.com/laravel/docs/location-api#reverse-geocoding
     *
     * Barikoi takes LONGITUDE before LATITUDE.
     *
     * @return array<string, mixed>|null
     */
    public function reverseGeocode(float $longitude, float $latitude, array $options = []): ?array
    {
        if (!$this->configured) {
            return null;
        }

        if (!$this->isValidCoordinate($latitude, $longitude)) {
            Log::warning('Barikoi reverseGeocode called with out-of-range coordinates.', [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return null;
        }

        $cacheKey = 'barikoi_reverse_' . md5($longitude . ',' . $latitude . '|' . json_encode($options));

        return Cache::remember($cacheKey, 600, function () use ($longitude, $latitude, $options) {
            try {
                $params = array_merge([
                    'api_key' => $this->apiKey,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                ], $options);

                // Convert boolean values to strings for the API
                $params = array_map(function ($value) {
                    return is_bool($value) ? ($value ? 'true' : 'false') : $value;
                }, $params);

                $response = Http::timeout(10)->acceptJson()->retry(2, 200)
                    ->get($this->baseUrl . '/v2/api/search/reverse/geocode', $params);

                if (!$response->successful()) {
                    Log::warning('Barikoi reverse geocode request failed.', [
                        'status' => $response->status(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                    ]);

                    return null;
                }

                $place = $response->json('place');

                if (!is_array($place)) {
                    return null;
                }

                return $this->normalizePlace((array) $place);
            } catch (\Throwable $e) {
                Log::error('Barikoi reverse geocode error: ' . $e->getMessage());

                return null;
            }
        });
    }

    /**
     * Shape a raw Barikoi place into a stable array for views and JSON.
     *
     * @return array<string, mixed>
     */
    protected function normalizePlace(array $place): array
    {
        $longitude = $place['longitude'] ?? $place['lon'] ?? $place['lng'] ?? null;
        $latitude = $place['latitude'] ?? $place['lat'] ?? null;

        return [
            'id' => $place['id'] ?? null,
            'address' => $place['address'] ?? '',
            'area' => $place['area'] ?? null,
            'city' => $place['city'] ?? null,
            'district' => $place['district'] ?? null,
            'division' => $place['division'] ?? null,
            'post_code' => $place['postCode'] ?? $place['post_code'] ?? null,
            'place_type' => $place['pType'] ?? $place['place_type'] ?? null,
            'longitude' => $longitude !== null ? (float) $longitude : null,
            'latitude' => $latitude !== null ? (float) $latitude : null,
        ];
    }

    /**
     * Reject coordinates that cannot be real before spending an API call.
     */
    protected function isValidCoordinate(float $latitude, float $longitude): bool
    {
        return $latitude >= -90 && $latitude <= 90
            && $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Get default map center (Dhaka).
     */
    public static function defaultCenter(): array
    {
        return ['lat' => 23.8103, 'lng' => 90.4125];
    }
}
