<?php

namespace Tests\Unit;

use App\Models\CreditWallet;
use App\Models\CreditTransaction;
use App\Models\ContactUnlock;
use App\Models\Listing;
use App\Models\User;
use App\Models\PropertyType;
use App\Models\Division;
use App\Models\District;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CreditService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreditService();
    }

    public function test_get_wallet_creates_wallet_if_missing(): void
    {
        $user = User::factory()->create();

        $wallet = $this->service->getWallet($user);

        $this->assertNotNull($wallet);
        $this->assertEquals($user->id, $wallet->user_id);
        $this->assertEquals(0, $wallet->balance);
    }

    public function test_get_wallet_returns_existing_wallet(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 100]);

        $wallet = $this->service->getWallet($user);

        $this->assertEquals(100, $wallet->balance);
    }

    public function test_get_balance(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 250]);

        $this->assertEquals(250, $this->service->getBalance($user));
    }

    public function test_add_credits(): void
    {
        $user = User::factory()->create();

        $transaction = $this->service->addCredits($user, 100, 'Test credit');

        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals('earn', $transaction->type);
        $this->assertEquals(100, $transaction->balance_after);
        $this->assertEquals(100, $this->service->getBalance($user));
    }

    public function test_add_credits_increments_balance(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 50]);

        $this->service->addCredits($user, 30, 'Bonus');

        $this->assertEquals(80, $this->service->getBalance($user));
    }

    public function test_add_credits_rejects_non_positive(): void
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->service->addCredits($user, 0, 'Zero');
    }

    public function test_deduct_for_unlock_success(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 200]);

        $listing = $this->createActiveListing();

        $transaction = $this->service->deductForUnlock($user, $listing);

        $this->assertEquals('spend', $transaction->type);
        $this->assertEquals($this->service->getUnlockCost(), $transaction->amount);
        $this->assertDatabaseHas('contact_unlocks', [
            'user_id' => $user->id,
            'lead_id' => $listing->id,
        ]);
    }

    public function test_deduct_for_unlock_insufficient_credits(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 5]); // Less than unlock cost

        $listing = $this->createActiveListing();

        $this->expectException(\App\Exceptions\InsufficientCreditException::class);
        $this->service->deductForUnlock($user, $listing);
    }

    public function test_get_transaction_history(): void
    {
        $user = User::factory()->create();
        CreditWallet::create(['user_id' => $user->id, 'balance' => 100]);

        $this->service->addCredits($user, 50, 'First');
        $this->service->addCredits($user, 30, 'Second');

        $history = $this->service->getTransactionHistory($user, 10);

        $this->assertEquals(2, $history->total());
    }

    protected function createActiveListing(): Listing
    {
        $user = User::factory()->create();
        $propertyType = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apt', 'icon' => '🏢']);
        $division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $division->id]);

        return Listing::create([
            'user_id' => $user->id,
            'property_type_id' => $propertyType->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'title' => 'Test Listing',
            'slug' => 'test-listing-' . uniqid(),
            'description' => 'A test listing',
            'listing_type' => 'rent',
            'price' => 20000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'address' => 'Test Address',
            'status' => 'active',
        ]);
    }
}
