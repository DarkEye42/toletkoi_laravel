<?php

namespace Tests\Unit;

use App\Models\Listing;
use App\Models\PropertyType;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use App\Models\User;
use App\Models\ListingFeature;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SearchService $service;
    protected $user;
    protected $propertyType;
    protected $division;
    protected $district;
    protected $area;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SearchService();

        $this->user = User::factory()->create();
        $this->propertyType = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apartment', 'icon' => '🏢']);
        $this->division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $this->district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $this->division->id]);
        $this->area = Area::create(['name' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'district_id' => $this->district->id]);
    }

    public function test_search_returns_active_listings(): void
    {
        $this->createListing(['status' => 'active', 'title' => 'Active Listing']);
        $this->createListing(['status' => 'pending', 'title' => 'Pending Listing']);

        $request = Request::create('/search');
        $results = $this->service->search($request)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Active Listing', $results->first()->title);
    }

    public function test_search_by_text(): void
    {
        $this->createListing(['title' => 'Gulshan Apartment']);
        $this->createListing(['title' => 'Banani House']);

        $request = Request::create('/search?q=Gulshan');
        $results = $this->service->search($request)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Gulshan Apartment', $results->first()->title);
    }

    public function test_search_by_division(): void
    {
        $this->createListing(['title' => 'Dhaka Place']);

        $otherDivision = Division::create(['name' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'slug' => 'chittagong']);
        $otherDistrict = District::create(['name' => 'CTG City', 'name_bn' => 'সিটি', 'slug' => 'ctg-city', 'division_id' => $otherDivision->id]);

        $this->createListing(['title' => 'CTG Place', 'division_id' => $otherDivision->id, 'district_id' => $otherDistrict->id]);

        $request = Request::create('/search?division_id=' . $this->division->id);
        $results = $this->service->search($request)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Dhaka Place', $results->first()->title);
    }

    public function test_search_by_price_range(): void
    {
        $this->createListing(['title' => 'Cheap', 'price' => 10000]);
        $this->createListing(['title' => 'Mid', 'price' => 25000]);
        $this->createListing(['title' => 'Expensive', 'price' => 50000]);

        $request = Request::create('/search?budget_min=15000&budget_max=30000');
        $results = $this->service->search($request)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Mid', $results->first()->title);
    }

    public function test_search_by_bedrooms(): void
    {
        $this->createListing(['title' => '1BR', 'bedrooms' => 1]);
        $this->createListing(['title' => '3BR', 'bedrooms' => 3]);

        $request = Request::create('/search?bedrooms=3');
        $results = $this->service->search($request)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('3BR', $results->first()->title);
    }

    public function test_get_featured(): void
    {
        $this->createListing(['title' => 'Featured', 'is_featured' => true]);
        $this->createListing(['title' => 'Normal']);

        $results = $this->service->getFeatured(10);

        $this->assertCount(1, $results);
        $this->assertEquals('Featured', $results->first()->title);
    }

    public function test_get_related(): void
    {
        $listing = $this->createListing(['title' => 'Original']);
        $this->createListing(['title' => 'Same Area', 'area_id' => $listing->area_id]);
        // Different district AND property type so it won't match any OR condition
        $otherPropertyType = PropertyType::create(['name' => 'House', 'name_bn' => 'বাড়ি', 'slug' => 'house', 'icon' => '🏠']);
        $otherDivision = Division::create(['name' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'slug' => 'chittagong']);
        $otherDistrict = District::create(['name' => 'CTG City', 'name_bn' => 'সিটি', 'slug' => 'ctg-city', 'division_id' => $otherDivision->id]);
        $this->createListing([
            'title' => 'Different Area',
            'property_type_id' => $otherPropertyType->id,
            'division_id' => $otherDivision->id,
            'district_id' => $otherDistrict->id,
            'area_id' => null,
        ]);

        $results = $this->service->getRelated($listing, 10);

        $this->assertCount(1, $results);
        $this->assertEquals('Same Area', $results->first()->title);
    }

    protected function createListing(array $overrides = []): Listing
    {
        $defaults = [
            'user_id' => $this->user->id,
            'property_type_id' => $this->propertyType->id,
            'division_id' => $this->division->id,
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'title' => 'Test Listing',
            'slug' => 'test-' . uniqid(),
            'description' => 'A test listing',
            'listing_type' => 'rent',
            'price' => 20000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'address' => 'Test Address',
            'status' => 'active',
        ];

        return Listing::create(array_merge($defaults, $overrides));
    }
}
