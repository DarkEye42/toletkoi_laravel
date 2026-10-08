<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\PropertyType;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use App\Models\User;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_homepage_shows_published_blog_posts_only(): void
    {
        $user = User::factory()->create();

        BlogPost::create([
            'user_id' => $user->id,
            'title' => 'Published article',
            'slug' => 'published-article',
            'content' => 'Published content',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::create([
            'user_id' => $user->id,
            'title' => 'Draft article',
            'slug' => 'draft-article',
            'content' => 'Draft content',
            'status' => 'draft',
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Published article')
            ->assertDontSee('Draft article');
    }

    public function test_about_page_loads(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }

    public function test_faq_page_loads(): void
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
    }

    public function test_contact_page_loads(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
    }

    public function test_pricing_page_loads(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
    }

    public function test_search_page_loads(): void
    {
        $response = $this->get('/search');
        $response->assertStatus(200);
    }

    public function test_map_search_page_loads(): void
    {
        $response = $this->get('/search/map');
        $response->assertStatus(200);
    }

    public function test_listings_index_loads(): void
    {
        $response = $this->get('/listings');
        $response->assertStatus(200);
    }

    public function test_listing_detail_loads(): void
    {
        $user = User::factory()->create();
        $propertyType = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apartment', 'icon' => '🏢']);
        $propertySubtype = \App\Models\PropertySubtype::create(['property_type_id' => $propertyType->id, 'name' => '2 BHK', 'name_bn' => '২ বিএইচকে', 'slug' => '2-bhk']);
        $division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $division->id]);
        $area = Area::create(['name' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'district_id' => $district->id]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'property_type_id' => $propertyType->id,
            'property_subtype_id' => $propertySubtype->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'area_id' => $area->id,
            'title' => 'Test Apartment in Gulshan',
            'slug' => 'test-apartment-in-gulshan',
            'description' => 'A beautiful apartment',
            'listing_type' => 'rent',
            'price' => 25000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'address' => '123 Gulshan Avenue',
            'status' => 'active',
        ]);

        $url = "/listing/{$district->slug}/{$area->slug}/{$propertyType->slug}/{$propertySubtype->slug}/{$listing->slug}";
        $response = $this->get($url);
        $response->assertStatus(200);
    }

    public function test_pending_listing_returns_404(): void
    {
        $user = User::factory()->create();
        $propertyType = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apartment', 'icon' => '🏢']);
        $propertySubtype = \App\Models\PropertySubtype::create(['property_type_id' => $propertyType->id, 'name' => '2 BHK', 'name_bn' => '২ বিএইচকে', 'slug' => '2-bhk']);
        $division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $division->id]);
        $area = Area::create(['name' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'district_id' => $district->id]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'property_type_id' => $propertyType->id,
            'property_subtype_id' => $propertySubtype->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'area_id' => $area->id,
            'title' => 'Pending Listing',
            'slug' => 'pending-listing',
            'description' => 'A pending listing',
            'listing_type' => 'rent',
            'price' => 20000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'address' => '456 Test Road',
            'status' => 'pending',
        ]);

        $url = "/listing/{$district->slug}/{$area->slug}/{$propertyType->slug}/{$propertySubtype->slug}/{$listing->slug}";
        $response = $this->get($url);
        $response->assertStatus(404);
    }

    public function test_sitemap_loads(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
    }
}
