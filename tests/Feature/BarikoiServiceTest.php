<?php

namespace Tests\Feature;

use App\Services\BarikoiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Barikoi location integration.
 *
 * HTTP is faked throughout so the suite never spends real API quota, but the
 * faked payloads mirror the live responses (verified against barikoi.xyz) so
 * the normalisation logic is exercised against the real shape.
 */
class BarikoiServiceTest extends TestCase
{
    /**
     * A realistic autocomplete payload as returned by
     * GET https://barikoi.xyz/v2/api/search/autocomplete/place
     */
    protected function autocompletePayload(): array
    {
        return [
            'places' => [
                [
                    'id' => 1763868,
                    'address' => 'Assistant Commissioner (Land) Office, Gulshan 2, Dhaka',
                    'area' => 'Gulshan',
                    'city' => 'Dhaka',
                    'district' => 'Dhaka',
                    'division' => 'Dhaka',
                    'postCode' => '1212',
                    'pType' => 'office',
                    'longitude' => '90.4152',
                    'latitude' => '23.7937',
                ],
                [
                    'id' => 1763869,
                    'address' => 'Gulshan 1 Circle, Dhaka',
                    'area' => 'Gulshan',
                    'city' => 'Dhaka',
                    'district' => 'Dhaka',
                    'division' => 'Dhaka',
                    'postCode' => '1212',
                    'pType' => 'road',
                    'longitude' => '90.4160',
                    'latitude' => '23.7805',
                ],
            ],
        ];
    }

    /**
     * A realistic reverse geocode payload as returned by
     * GET https://barikoi.xyz/v2/api/search/reverse/geocode
     */
    protected function reversePayload(): array
    {
        return [
            'place' => [
                'id' => 1813216,
                'address' => 'Hospital, Lane 11 West, Baridhara Dohs, Baridhara, Dhaka',
                'area' => 'Baridhara',
                'city' => 'Dhaka',
                'district' => 'Dhaka',
                'division' => 'Dhaka',
                'postCode' => '1206',
                'longitude' => '90.4125',
                'latitude' => '23.8103',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.barikoi.key' => 'test-key']);
    }

    // =====================================================
    // CONFIGURATION
    // =====================================================

    public function test_service_reports_configured_when_key_is_present(): void
    {
        $service = app(BarikoiService::class);

        $this->assertTrue($service->isConfigured());
        $this->assertSame('test-key', $service->getApiKey());
        $this->assertNull($service->getTileUrl(), 'Raster tiles deprecated — getTileUrl() returns null for OSM fallback');
    }

    public function test_service_reports_unconfigured_without_key(): void
    {
        config(['services.barikoi.key' => null]);
        Cache::flush();

        $service = new BarikoiService();

        $this->assertFalse($service->isConfigured());
        $this->assertSame('', $service->getApiKey());
        $this->assertNull($service->getTileUrl(), 'Tile URL must be null so views fall back to OSM.');
    }

    public function test_lookups_are_skipped_entirely_when_unconfigured(): void
    {
        config(['services.barikoi.key' => null]);
        Http::fake();

        $service = new BarikoiService();

        $this->assertSame([], $service->autocomplete('Gulshan'));
        $this->assertNull($service->geocode('Gulshan, Dhaka'));
        $this->assertNull($service->reverseGeocode(90.4125, 23.8103));

        Http::assertNothingSent();
    }

    // =====================================================
    // AUTOCOMPLETE
    // =====================================================

    public function test_autocomplete_returns_normalised_places(): void
    {
        Http::fake([
            'barikoi.xyz/v2/api/search/autocomplete/place*' => Http::response($this->autocompletePayload(), 200),
        ]);

        $places = app(BarikoiService::class)->autocomplete('Gulshan', 2);

        $this->assertCount(2, $places);
        $this->assertSame('Gulshan', $places[0]['area']);
        $this->assertSame('Dhaka', $places[0]['district']);
        $this->assertSame(90.4152, $places[0]['longitude']);
        $this->assertSame(23.7937, $places[0]['latitude']);
    }

    public function test_autocomplete_calls_the_correct_endpoint_with_api_key_parameter(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->autocompletePayload(), 200),
        ]);

        app(BarikoiService::class)->autocomplete('Gulshan');

        Http::assertSent(function ($request) {
            // The host and the `api_key` parameter name are the two details
            // that were previously wrong and broke every request.
            return str_contains($request->url(), 'barikoi.xyz/v2/api/search/autocomplete/place')
                && $request['api_key'] === 'test-key'
                && $request['q'] === 'Gulshan';
        });
    }

    public function test_autocomplete_requires_at_least_two_characters(): void
    {
        Http::fake();

        $this->assertSame([], app(BarikoiService::class)->autocomplete('a'));

        Http::assertNothingSent();
    }

    public function test_autocomplete_returns_empty_on_provider_failure(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response('Server Error', 500),
        ]);

        $this->assertSame([], app(BarikoiService::class)->autocomplete('Gulshan'));
    }

    public function test_autocomplete_respects_the_limit(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->autocompletePayload(), 200),
        ]);

        $this->assertCount(1, app(BarikoiService::class)->autocomplete('Gulshan', 1));
    }

    // =====================================================
    // REVERSE GEOCODING
    // =====================================================

    public function test_reverse_geocode_returns_a_normalised_place(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->reversePayload(), 200),
        ]);

        $place = app(BarikoiService::class)->reverseGeocode(90.4125, 23.8103);

        $this->assertNotNull($place);
        $this->assertSame('Baridhara', $place['area']);
        $this->assertStringContainsString('Baridhara', $place['address']);
    }

    public function test_reverse_geocode_sends_longitude_before_latitude(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->reversePayload(), 200),
        ]);

        // Call as (longitude, latitude) - Barikoi's documented order.
        app(BarikoiService::class)->reverseGeocode(90.4125, 23.8103);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/search/reverse/geocode')
                && (float) $request['longitude'] === 90.4125
                && (float) $request['latitude'] === 23.8103
                && $request['api_key'] === 'test-key';
        });
    }

    public function test_reverse_geocode_rejects_out_of_range_coordinates(): void
    {
        Http::fake();

        $service = app(BarikoiService::class);

        $this->assertNull($service->reverseGeocode(999.0, 23.8103));
        $this->assertNull($service->reverseGeocode(90.4125, 999.0));

        Http::assertNothingSent();
    }

    public function test_reverse_geocode_returns_null_when_place_missing(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response(['status' => 'ok'], 200),
        ]);

        $this->assertNull(app(BarikoiService::class)->reverseGeocode(90.4125, 23.8103));
    }

    // =====================================================
    // GEOCODING (Rupantor)
    // =====================================================

    /**
     * A realistic Rupantor geocode payload as returned by
     * POST https://barikoi.xyz/v2/api/search/rupantor/geocode
     */
    protected function geocodePayload(): array
    {
        return [
            'given_address' => 'shawrapara',
            'fixed_address' => 'shawrapara',
            'address_status' => 'complete',
            'confidence_score_percentage' => 95,
            'status' => 200,
            'geocoded_address' => [
                'latitude' => '23.7459408',
                'longitude' => '90.37546663',
                'geo_location' => [90.37546663, 23.7459408],
                'address' => 'Shawrapara, Mirpur, Dhaka',
                'area' => 'Mirpur',
                'city' => 'Dhaka',
                'postcode' => 1216,
                'district' => 'Dhaka',
            ],
        ];
    }

    public function test_geocode_returns_normalised_place_from_rupantor(): void
    {
        Http::fake([
            'barikoi.xyz/v2/api/search/rupantor/geocode' => Http::response($this->geocodePayload(), 200),
        ]);

        $place = app(BarikoiService::class)->geocode('Shawrapara, Dhaka');

        $this->assertNotNull($place);
        $this->assertSame('Shawrapara, Mirpur, Dhaka', $place['address']);
        $this->assertSame('Mirpur', $place['area']);
        $this->assertSame('Dhaka', $place['city']);
        $this->assertSame(90.37546663, $place['longitude']);
        $this->assertSame(23.7459408, $place['latitude']);
    }

    public function test_geocode_calls_rupantor_endpoint(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->geocodePayload(), 200),
        ]);

        app(BarikoiService::class)->geocode('Shawrapara');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v2/api/search/rupantor/geocode')
                && $request['api_key'] === 'test-key'
                && $request['q'] === 'Shawrapara';
        });
    }

    public function test_geocode_returns_null_for_an_empty_address(): void
    {
        Http::fake();

        $this->assertNull(app(BarikoiService::class)->geocode('   '));

        Http::assertNothingSent();
    }

    // =====================================================
    // CACHING
    // =====================================================

    public function test_repeated_autocomplete_calls_hit_the_provider_once(): void
    {
        Http::fake([
            'barikoi.xyz/*' => Http::response($this->autocompletePayload(), 200),
        ]);

        $service = app(BarikoiService::class);
        $service->autocomplete('Gulshan');
        $service->autocomplete('Gulshan');

        Http::assertSentCount(1);
    }
}
