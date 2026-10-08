<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\Listing;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Phase 03 admin panel: access control and listing moderation.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // The roles table has a NOT NULL slug column, so findOrCreate() (which
        // only sets name/guard_name) cannot be used here.
        $role = Role::where('name', 'Admin')->first()
            ?? Role::create(['name' => 'Admin', 'slug' => 'admin', 'guard_name' => 'web']);

        $user->assignRole($role);

        return $user;
    }

    protected function makeLandlord(): User
    {
        return User::factory()->create(['email_verified_at' => now()]);
    }

    protected function makeListing(User $owner, array $overrides = []): Listing
    {
        $type = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apartment', 'icon' => 'apartment']);
        $division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $division->id]);
        $area = Area::create(['name' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'district_id' => $district->id]);

        return Listing::create(array_merge([
            'user_id' => $owner->id,
            'property_type_id' => $type->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'area_id' => $area->id,
            'title' => 'Admin Panel Test Listing',
            'slug' => 'admin-panel-test-listing-' . uniqid(),
            'description' => 'A listing for admin moderation tests.',
            'listing_type' => 'rent',
            'price' => 25000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'address' => '123 Test Road',
            'status' => 'pending',
        ], $overrides));
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $this->actingAs($this->makeLandlord());

        $this->get('/admin')->assertStatus(403);
        $this->get('/admin/listings')->assertStatus(403);
    }

    public function test_admin_can_load_dashboard(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get('/admin')->assertOk()->assertSee('Admin Dashboard');
    }

    public function test_admin_can_load_listing_queue(): void
    {
        $this->actingAs($this->makeAdmin());
        $this->makeListing($this->makeLandlord());

        $this->get('/admin/listings')->assertOk()->assertSee('Admin Panel Test Listing');
    }

    public function test_admin_can_view_listing_detail(): void
    {
        $this->actingAs($this->makeAdmin());
        $listing = $this->makeListing($this->makeLandlord());

        $this->get('/admin/listings/' . $listing->id)
            ->assertOk()
            ->assertSee($listing->title);
    }

    public function test_admin_can_approve_a_pending_listing(): void
    {
        $this->actingAs($this->makeAdmin());
        $listing = $this->makeListing($this->makeLandlord());

        $this->post('/admin/listings/' . $listing->id . '/approve')
            ->assertRedirect();

        $this->assertSame('active', $listing->fresh()->status);
    }

    public function test_admin_can_reject_a_listing_with_reason(): void
    {
        $this->actingAs($this->makeAdmin());
        $listing = $this->makeListing($this->makeLandlord());

        $this->post('/admin/listings/' . $listing->id . '/reject', [
            'rejection_reason' => 'Photos do not match the property.',
        ])->assertRedirect();

        $listing->refresh();
        $this->assertSame('rejected', $listing->status);
        $this->assertSame('Photos do not match the property.', $listing->rejection_reason);
    }

    public function test_rejection_requires_a_reason(): void
    {
        $this->actingAs($this->makeAdmin());
        $listing = $this->makeListing($this->makeLandlord());

        $this->post('/admin/listings/' . $listing->id . '/reject', [])
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame('pending', $listing->fresh()->status);
    }

    public function test_admin_can_toggle_featured_flag(): void
    {
        $this->actingAs($this->makeAdmin());
        $listing = $this->makeListing($this->makeLandlord(), ['status' => 'active']);

        $this->post('/admin/listings/' . $listing->id . '/feature')->assertRedirect();
        $this->assertTrue($listing->fresh()->is_featured);

        $this->post('/admin/listings/' . $listing->id . '/feature')->assertRedirect();
        $this->assertFalse($listing->fresh()->is_featured);
    }

    public function test_non_admin_cannot_moderate(): void
    {
        $listing = $this->makeListing($this->makeLandlord());

        $this->actingAs($this->makeLandlord())
            ->post('/admin/listings/' . $listing->id . '/approve')
            ->assertStatus(403);

        $this->assertSame('pending', $listing->fresh()->status);
    }
}
