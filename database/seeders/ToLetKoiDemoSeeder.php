<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Area;
use App\Models\BlogPost;
use App\Models\CreditTransaction;
use App\Models\CreditWallet;
use App\Models\District;
use App\Models\Division;
use App\Models\Favorite;
use App\Models\Lead;
use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingStat;
use App\Models\ListingView;
use App\Models\PaymentTransaction;
use App\Models\PropertyImage;
use App\Models\PropertySubtype;
use App\Models\PropertyType;
use App\Models\SavedSearch;
use App\Models\ServiceCategory;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ToLetKoiDemoSeeder extends Seeder
{
	public function run(): void
	{
		DB::transaction(function (): void {
			$roles = Role::query()->get()->keyBy('slug');
			$areas = $this->locations();
			$users = $this->seedUsers($roles, $areas);

			$this->seedCredits($users['credit_users']);
			$listings = $this->seedListings($users['owners'], $users['tenants'], $areas);
			$this->seedActivity($users['tenants'], $listings);
			$this->seedCms($users['admin']);
			$this->seedServices($users['service_providers'], $areas);
			$this->seedOrganizations($users, $areas);
			$this->seedSubscription($users['landlords'][0]);
		});

		$this->command?->info('ToLetKoi demo dataset seeded idempotently.');
	}

	private function locations(): array
	{
		$locations = [
			'mirpur-model' => ['district' => 'dhaka-district', 'name' => 'Mirpur Model', 'name_bn' => 'মিরপুর', 'lat' => 23.80404070, 'lng' => 90.35681720],
			'uttara' => ['district' => 'dhaka-district', 'name' => 'Uttara', 'name_bn' => 'উত্তরা', 'lat' => 23.8759, 'lng' => 90.3795],
			'badda' => ['district' => 'dhaka-district', 'name' => 'Badda', 'name_bn' => 'বাড্ডা', 'lat' => 23.7736, 'lng' => 90.4303],
			'rampura' => ['district' => 'dhaka-district', 'name' => 'Rampura', 'name_bn' => 'রামপুরা', 'lat' => 23.7736, 'lng' => 90.4253],
			'dhanmondi' => ['district' => 'dhaka-district', 'name' => 'Dhanmondi', 'name_bn' => 'ধানমন্ডি', 'lat' => 23.7461, 'lng' => 90.3742],
			'mohammadpur-dhaka-district' => ['district' => 'dhaka-district', 'name' => 'Mohammadpur', 'name_bn' => 'মোহাম্মদপুর', 'lat' => 23.76457840, 'lng' => 90.35101530],
			'jatrabari' => ['district' => 'dhaka-district', 'name' => 'Jatrabari', 'name_bn' => 'যাত্রাবাড়ী', 'lat' => 23.7104, 'lng' => 90.4308],
			'tejgaon' => ['district' => 'dhaka-district', 'name' => 'Tejgaon', 'name_bn' => 'তেজগাঁও', 'lat' => 23.7578, 'lng' => 90.3864],
			'banani' => ['district' => 'dhaka-district', 'name' => 'Banani', 'name_bn' => 'বনানী', 'lat' => 23.7936, 'lng' => 90.4023],
			'motijheel' => ['district' => 'dhaka-district', 'name' => 'Motijheel', 'name_bn' => 'মতিঝিল', 'lat' => 23.7337, 'lng' => 90.4178],
			'tejgaon' => ['district' => 'dhaka-district', 'name' => 'Tejgaon', 'name_bn' => 'তেজগাঁও', 'lat' => 23.7588, 'lng' => 90.3944],
			'khilgaon' => ['district' => 'dhaka-district', 'name' => 'Khilgaon', 'name_bn' => 'খিলগাঁও', 'lat' => 23.7562, 'lng' => 90.4347],
			'gulshan' => ['district' => 'dhaka-district', 'name' => 'Gulshan', 'name_bn' => 'গুলশান', 'lat' => 23.7925, 'lng' => 90.4078],
			'vatara' => ['district' => 'dhaka-district', 'name' => 'Vatara', 'name_bn' => 'ভাটারা', 'lat' => 23.8186, 'lng' => 90.4353],
			'agrabad' => ['district' => 'chattogram-district', 'name' => 'Agrabad', 'name_bn' => 'আগ্রাবাদ', 'lat' => 22.3333, 'lng' => 91.8333],
			'nasirabad' => ['district' => 'chattogram-district', 'name' => 'Nasirabad', 'name_bn' => 'নাসিরাবাদ', 'lat' => 22.3500, 'lng' => 91.8000],
			'rajshahi' => ['district' => 'rajshahi-district', 'name' => 'Rajshahi City', 'name_bn' => 'রাজশাহী শহর', 'lat' => 24.3636, 'lng' => 88.6241],
			'sylhet' => ['district' => 'sylhet-district', 'name' => 'Sylhet City', 'name_bn' => 'সিলেট শহর', 'lat' => 24.8949, 'lng' => 91.8687],
			'khulna' => ['district' => 'khulna-district', 'name' => 'Khulna City', 'name_bn' => 'খুলনা শহর', 'lat' => 22.8456, 'lng' => 89.5403],
			'rangpur' => ['district' => 'rangpur-district', 'name' => 'Rangpur City', 'name_bn' => 'রংপুর শহর', 'lat' => 25.7439, 'lng' => 89.2752],
		];

		return collect($locations)->mapWithKeys(function (array $location, string $slug): array {
			$district = District::where('slug', $location['district'])->firstOrFail();
			$area = Area::firstOrCreate(['slug' => $slug], [
				'district_id' => $district->id,
				'name' => $location['name'],
				'name_bn' => $location['name_bn'],
				'lat' => $location['lat'],
				'lng' => $location['lng'],
				'is_popular' => in_array($slug, ['mirpur-model', 'uttara', 'badda', 'dhanmondi', 'gulshan'], true),
				'is_active' => true,
			]);
			return [$slug => $area];
		})->all();
	}

	private function seedUsers($roles, array $areas): array
	{
		$definitions = [
			['key' => 'admin', 'email' => 'demo-admin-1@toletkoi.test', 'name' => 'Demo Operations Admin', 'role' => 'admin'],
			['key' => 'admin-2', 'email' => 'demo-admin-2@toletkoi.test', 'name' => 'Demo Content Admin', 'role' => 'admin'],
		];

		for ($index = 1; $index <= 5; $index++) {
			$definitions[] = ['key' => "landlord-{$index}", 'email' => "demo-landlord-{$index}@toletkoi.test", 'name' => "Demo Landlord {$index}", 'role' => 'landlord'];
		}

		for ($index = 1; $index <= 5; $index++) {
			$definitions[] = ['key' => "renter-{$index}", 'email' => "demo-renter-{$index}@toletkoi.test", 'name' => "Demo Renter {$index}", 'role' => 'tenant'];
			$definitions[] = ['key' => "tenant-{$index}", 'email' => "demo-tenant-{$index}@toletkoi.test", 'name' => "Demo Tenant {$index}", 'role' => 'tenant'];
		}

		for ($index = 1; $index <= 2; $index++) {
			$definitions[] = ['key' => "agent-{$index}", 'email' => "demo-agent-{$index}@toletkoi.test", 'name' => "Demo Agent {$index}", 'role' => 'agent'];
			$definitions[] = ['key' => "service-provider-{$index}", 'email' => "demo-provider-{$index}@toletkoi.test", 'name' => "Demo Service Provider {$index}", 'role' => 'service_provider'];
		}

		$users = [];
		foreach ($definitions as $index => $definition) {
			$user = User::firstOrNew(['email' => $definition['email']]);
			$user->fill([
				'name' => $definition['name'],
				'password' => Hash::make('demo-password'),
			]);
			$user->forceFill(['email_verified_at' => now()])->save();

			if ($roles->has($definition['role'])) {
				$user->syncRoles([$roles->get($definition['role'])]);
			}

			$area = array_values($areas)[$index % count($areas)];
			UserProfile::updateOrCreate(['user_id' => $user->id], [
				'phone' => '+880170000' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
				'bio' => 'Synthetic ToLetKoi development profile.',
				'address' => $area->name . ', Bangladesh',
				'address_bn' => $area->name_bn . ', বাংলাদেশ',
				'division_id' => $area->district->division_id,
				'district_id' => $area->district_id,
				'area_id' => $area->id,
				'phone_verified_at' => now(),
			]);
			UserPreference::updateOrCreate(['user_id' => $user->id], [
				'language' => 'en',
				'currency' => 'BDT',
				'theme' => 'light',
				'email_notifications' => true,
				'sms_notifications' => true,
				'push_notifications' => true,
			]);

			$users[$definition['key']] = $user;
		}

		$landlords = collect($users)->filter(fn (User $user, string $key) => str_starts_with($key, 'landlord-'))->values()->all();
		$agents = collect($users)->filter(fn (User $user, string $key) => str_starts_with($key, 'agent-'))->values()->all();
		$tenants = collect($users)->filter(fn (User $user, string $key) => str_starts_with($key, 'renter-') || str_starts_with($key, 'tenant-'))->values()->all();
		$providers = collect($users)->filter(fn (User $user, string $key) => str_starts_with($key, 'service-provider-'))->values()->all();

		return [
			'admin' => $users['admin'],
			'landlords' => $landlords,
			'owners' => array_merge($landlords, $agents),
			'tenants' => $tenants,
			'service_providers' => $providers,
			'credit_users' => array_merge($landlords, $tenants),
		];
	}

	private function seedListings(array $owners, array $tenants, array $areas): array
	{
		$types = PropertyType::query()->get()->keyBy('slug');
		$subtypes = PropertySubtype::query()->get()->keyBy('slug');
		$specs = [
			['slug' => 'demo-family-mirpur-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'mirpur-model', 'title' => 'Bright 3 Bedroom Family Flat in Mirpur', 'price' => 28000, 'bedrooms' => 3, 'bathrooms' => 2, 'sqft' => 1250],
			['slug' => 'demo-family-uttara-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'uttara', 'title' => 'Well-Planned Family Apartment near Uttara Sector 7', 'price' => 35000, 'bedrooms' => 3, 'bathrooms' => 3, 'sqft' => 1450],
			['slug' => 'demo-family-badda-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'badda', 'title' => 'Quiet 2 Bedroom Family Home in Badda', 'price' => 22000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 950],
			['slug' => 'demo-family-rampura-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'rampura', 'title' => 'Family Flat with Lift Access in Rampura', 'price' => 18000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 900],
			['slug' => 'demo-family-dhanmondi-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'dhanmondi', 'title' => 'Sunlit 3 Bedroom Residence near Dhanmondi Lake', 'price' => 55000, 'bedrooms' => 3, 'bathrooms' => 3, 'sqft' => 1650],
			['slug' => 'demo-family-mohammadpur-2br', 'type' => 'house', 'subtype' => 'single-family-home', 'area' => 'mohammadpur-dhaka-district', 'title' => 'Family Home with Courtyard in Mohammadpur', 'price' => 26000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 1100],
			['slug' => 'demo-family-jatrabari-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'jatrabari', 'title' => 'Spacious Family Apartment near Jatrabari Main Road', 'price' => 16000, 'bedrooms' => 3, 'bathrooms' => 2, 'sqft' => 1200],
			['slug' => 'demo-family-gulshan-4br', 'type' => 'apartment', 'subtype' => '4-plus-bhk-apartment', 'area' => 'gulshan', 'title' => 'Premium 4 Bedroom Apartment in Gulshan', 'price' => 68000, 'bedrooms' => 4, 'bathrooms' => 4, 'sqft' => 2300],
			['slug' => 'demo-family-vatara-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'vatara', 'title' => 'Modern Family Flat in Vatara Residential Area', 'price' => 42000, 'bedrooms' => 3, 'bathrooms' => 3, 'sqft' => 1550],
			['slug' => 'demo-family-khilgaon-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'khilgaon', 'title' => 'Affordable 2 Bedroom Home in Khilgaon', 'price' => 17000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 880],
			['slug' => 'demo-bachelor-tejgaon-seat', 'type' => 'room', 'subtype' => 'shared-room', 'area' => 'tejgaon', 'title' => 'Furnished Bachelor Seat near Tejgaon Metro', 'price' => 4500, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 180],
			['slug' => 'demo-bachelor-badda-room', 'type' => 'room', 'subtype' => 'single-room', 'area' => 'badda', 'title' => 'Private Bachelor Room with Attached Bath in Badda', 'price' => 8500, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 320],
			['slug' => 'demo-bachelor-mirpur-seat', 'type' => 'room', 'subtype' => 'shared-room', 'area' => 'mirpur-model', 'title' => 'Budget Bachelor Seat with Wi-Fi in Mirpur', 'price' => 3000, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 160],
			['slug' => 'demo-bachelor-uttara-room', 'type' => 'room', 'subtype' => 'single-room', 'area' => 'uttara', 'title' => 'Single Bachelor Room near Uttara Bus Stand', 'price' => 7500, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 300],
			['slug' => 'demo-bachelor-dhanmondi-seat', 'type' => 'room', 'subtype' => 'shared-room', 'area' => 'dhanmondi', 'title' => 'Student-Friendly Seat near Dhanmondi College', 'price' => 5500, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 190],
			['slug' => 'demo-sublet-badda-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'badda', 'title' => 'Two-Month Sublet in a Ready Apartment at Badda', 'price' => 12000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 900],
			['slug' => 'demo-sublet-uttara-1br', 'type' => 'apartment', 'subtype' => '1-bhk-apartment', 'area' => 'uttara', 'title' => 'Short-Term Furnished Sublet in Uttara', 'price' => 15000, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 650],
			['slug' => 'demo-sublet-rampura-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'rampura', 'title' => 'Family Sublet with Balcony in Rampura', 'price' => 10000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 850],
			['slug' => 'demo-sublet-mirpur-1br', 'type' => 'apartment', 'subtype' => '1-bhk-apartment', 'area' => 'mirpur-model', 'title' => 'One Bedroom Sublet near Mirpur Shopping District', 'price' => 9000, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 600],
			['slug' => 'demo-sublet-khilgaon-room', 'type' => 'room', 'subtype' => 'master-bedroom', 'area' => 'khilgaon', 'title' => 'Master Room Sublet with Shared Kitchen in Khilgaon', 'price' => 8000, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 400],
			['slug' => 'demo-office-banani-private', 'type' => 'office-space', 'subtype' => 'private-office', 'area' => 'banani', 'title' => 'Move-In Ready Private Office in Banani', 'price' => 65000, 'bedrooms' => 0, 'bathrooms' => 2, 'sqft' => 1500],
			['slug' => 'demo-office-motijheel-private', 'type' => 'office-space', 'subtype' => 'private-office', 'area' => 'motijheel', 'title' => 'Compact Commercial Office near Motijheel', 'price' => 38000, 'bedrooms' => 0, 'bathrooms' => 1, 'sqft' => 850],
			['slug' => 'demo-office-tejgaon-coworking', 'type' => 'office-space', 'subtype' => 'co-working-space', 'area' => 'tejgaon', 'title' => 'Flexible Co-working Floor in Tejgaon', 'price' => 24000, 'bedrooms' => 0, 'bathrooms' => 2, 'sqft' => 700],
			['slug' => 'demo-office-agrabad-private', 'type' => 'office-space', 'subtype' => 'private-office', 'area' => 'agrabad', 'title' => 'Business Office Suite in Agrabad', 'price' => 45000, 'bedrooms' => 0, 'bathrooms' => 2, 'sqft' => 1100],
			['slug' => 'demo-hostel-mirpur-seat', 'type' => 'room', 'subtype' => 'shared-room', 'area' => 'mirpur-model', 'title' => 'Secure Hostel Seat for Students in Mirpur', 'price' => 2500, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 140],
			['slug' => 'demo-hostel-rajshahi-seat', 'type' => 'room', 'subtype' => 'shared-room', 'area' => 'rajshahi', 'title' => 'University Area Hostel Seat in Rajshahi', 'price' => 2200, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 150],
			['slug' => 'demo-hostel-sylhet-room', 'type' => 'room', 'subtype' => 'single-room', 'area' => 'sylhet', 'title' => 'Clean Single Hostel Room near Sylhet City Centre', 'price' => 6000, 'bedrooms' => 1, 'bathrooms' => 1, 'sqft' => 260],
			['slug' => 'demo-apartment-khulna-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'khulna', 'title' => 'New 2 Bedroom Apartment in Khulna', 'price' => 14000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 900],
			['slug' => 'demo-apartment-rangpur-3br', 'type' => 'apartment', 'subtype' => '3-bhk-apartment', 'area' => 'rangpur', 'title' => 'Family Apartment with Parking in Rangpur', 'price' => 18000, 'bedrooms' => 3, 'bathrooms' => 2, 'sqft' => 1200],
			['slug' => 'demo-apartment-nasirabad-2br', 'type' => 'apartment', 'subtype' => '2-bhk-apartment', 'area' => 'nasirabad', 'title' => 'Convenient 2 Bedroom Apartment in Nasirabad', 'price' => 26000, 'bedrooms' => 2, 'bathrooms' => 2, 'sqft' => 1000],
		];

		$listings = [];
		foreach ($specs as $index => $spec) {
			$area = $areas[$spec['area']];
			$owner = $owners[$index % count($owners)];
			$type = $types->get($spec['type']);
			$subtype = $subtypes->get($spec['subtype']);
			$active = $index < 28;
			$listing = Listing::updateOrCreate(['slug' => $spec['slug']], [
				'user_id' => $owner->id,
				'property_type_id' => $type?->id,
				'property_subtype_id' => $subtype?->id,
				'division_id' => $area->district->division_id,
				'district_id' => $area->district_id,
				'area_id' => $area->id,
				'title' => $spec['title'],
				'title_bn' => 'ডেমো ভাড়া বাসা - ' . $area->name_bn,
				'description' => 'A synthetic ToLetKoi demo listing with clear rental terms, practical amenities, and a convenient neighborhood setting.',
				'description_bn' => 'বাসা খোঁজার জন্য তৈরি করা একটি কৃত্রিম ডেমো তালিকা।',
				'listing_type' => 'rent',
				'price' => $spec['price'],
				'price_type' => 'monthly',
				'bedrooms' => $spec['bedrooms'],
				'bathrooms' => $spec['bathrooms'],
				'balconies' => $spec['bedrooms'] > 1 ? 2 : 1,
				'floor_level' => ($index % 7) + 1,
				'total_floors' => 8,
				'area_sqft' => $spec['sqft'],
				'furnishing_status' => $index % 3 === 0 ? 'furnished' : 'semi_furnished',
				'address' => $area->name . ', Bangladesh',
				'address_bn' => $area->name_bn . ', বাংলাদেশ',
				'lat' => $area->lat,
				'lng' => $area->lng,
				'available_from' => now()->addDays($index % 10)->toDateString(),
				'is_immediately_available' => $index % 4 !== 0,
				'status' => $active ? 'active' : 'pending',
				'is_featured' => $index < 6,
				'featured_until' => $index < 6 ? now()->addMonth() : null,
				'is_promoted' => $index < 3,
				'promoted_until' => $index < 3 ? now()->addWeeks(2) : null,
				'contact_name' => $owner->name,
				'contact_phone' => $owner->profile?->phone,
				'contact_email' => $owner->email,
				'meta_title' => $spec['title'],
				'meta_description' => 'Synthetic rental listing for ToLetKoi development and QA.',
			]);
			$listings[] = $listing;

			foreach (['home', 'interior'] as $order => $imageType) {
				PropertyImage::updateOrCreate(
					['listing_id' => $listing->id, 'sort_order' => $order],
					['path' => "demo/listings/{$imageType}.svg", 'original_name' => "{$imageType}.svg", 'is_primary' => $order === 0]
				);
			}

			ListingFeature::updateOrCreate(['listing_id' => $listing->id], [
				'has_parking' => $index % 3 === 0,
				'has_lift' => true,
				'has_generator' => $index % 4 !== 0,
				'has_gas' => $index % 5 !== 0,
				'electricity_included' => false,
				'water_included' => true,
				'has_security' => true,
				'has_cctv' => $index % 3 !== 1,
				'has_wifi' => $index % 4 === 1,
				'is_furnished' => $index % 3 === 0,
				'has_ac' => $index % 4 === 0,
				'has_washing_machine' => $index % 5 === 0,
				'shared_kitchen' => $spec['bedrooms'] === 1,
				'attached_bathroom' => true,
			]);
			ListingStat::updateOrCreate(['listing_id' => $listing->id], [
				'view_count' => 25 + ($index * 7),
				'contact_count' => 2 + ($index % 5),
				'favorite_count' => 1 + ($index % 4),
				'inquiry_count' => 1 + ($index % 3),
				'last_viewed_at' => now()->subHours($index + 1),
			]);
		}

		return $listings;
	}

	private function seedCredits(array $users): void
	{
		foreach ($users as $user) {
			$wallet = CreditWallet::updateOrCreate(['user_id' => $user->id], [
				'balance' => 195,
				'total_earned' => 50,
				'total_spent' => 5,
				'total_purchased' => 150,
				'total_refunded' => 0,
			]);
			CreditTransaction::updateOrCreate(['user_id' => $user->id, 'description' => 'Demo welcome bonus'], [
				'wallet_id' => $wallet->id, 'type' => 'bonus', 'amount' => 50, 'balance_after' => 50,
			]);
			CreditTransaction::updateOrCreate(['user_id' => $user->id, 'description' => 'Demo credit package purchase'], [
				'wallet_id' => $wallet->id, 'type' => 'purchase', 'amount' => 150, 'balance_after' => 200,
			]);
			CreditTransaction::updateOrCreate(['user_id' => $user->id, 'description' => 'Demo contact unlock'], [
				'wallet_id' => $wallet->id, 'type' => 'spend', 'amount' => -5, 'balance_after' => 195,
			]);
		}
	}

	private function seedActivity(array $tenants, array $listings): void
	{
		foreach ($tenants as $index => $tenant) {
			$listing = $listings[$index % count($listings)];
			Favorite::firstOrCreate(['user_id' => $tenant->id, 'listing_id' => $listing->id]);
			SavedSearch::updateOrCreate(['user_id' => $tenant->id, 'name' => 'Demo Dhaka rental search'], [
				'criteria' => ['listing_type' => 'rent', 'division' => 'dhaka', 'max_price' => 35000],
				'notify_new_matches' => true,
				'match_count' => 12,
				'last_checked_at' => now()->subHours(6),
			]);
			Lead::firstOrCreate(
				['listing_id' => $listing->id, 'user_id' => $tenant->id, 'message' => 'Please share viewing availability for this demo listing.'],
				['owner_id' => $listing->user_id, 'status' => 'new', 'phone' => $tenant->profile?->phone, 'email' => $tenant->email]
			);
			ListingView::firstOrCreate([
				'listing_id' => $listing->id,
				'user_id' => $tenant->id,
				'ip_address' => '10.0.0.' . ($index + 10),
			], ['user_agent' => 'ToLetKoi Demo Browser', 'referer' => 'demo-seed']);
			ActivityLog::updateOrCreate([
				'user_id' => $tenant->id,
				'type' => 'demo_seed_view',
				'subject_type' => Listing::class,
				'subject_id' => $listing->id,
			], ['properties' => ['source' => 'ToLetKoiDemoSeeder'], 'ip_address' => '10.0.0.' . ($index + 10)]);
		}
	}

	private function seedCms(User $admin): void
	{
		$pages = [
			['slug' => 'about-toletkoi', 'title' => 'About ToLetKoi', 'content' => 'ToLetKoi helps people discover practical rental options across Bangladesh.', 'content_bn' => 'ToLetKoi বাংলাদেশজুড়ে সহজে ভাড়ার বাসা খুঁজে পেতে সহায়তা করে।'],
			['slug' => 'rental-safety', 'title' => 'Rental Safety Guide', 'content' => 'Verify ownership, inspect the property, and keep written records before paying.', 'content_bn' => 'মালিকানা যাচাই, সম্পত্তি পরিদর্শন এবং অর্থ দেওয়ার আগে লিখিত রেকর্ড রাখুন।'],
		];
		foreach ($pages as $page) {
			DB::table('cms_pages')->updateOrInsert(['slug' => $page['slug']], array_merge($page, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]));
		}

		$posts = [
			['slug' => 'find-right-rental-home-dhaka', 'title' => 'How to Find the Right Rental Home in Dhaka', 'title_bn' => 'ঢাকায় সঠিক ভাড়ার বাসা খুঁজে পাওয়ার উপায়'],
			['slug' => 'rental-safety-checklist-tenants', 'title' => 'Rental Safety Checklist for Tenants', 'title_bn' => 'ভাড়াটিয়াদের জন্য নিরাপত্তা যাচাই তালিকা'],
			['slug' => 'verify-rental-property-before-paying', 'title' => 'How to Verify a Rental Property Before Paying', 'title_bn' => 'অগ্রিম দেওয়ার আগে ভাড়ার সম্পত্তি যাচাই'],
			['slug' => 'family-flat-or-bachelor-seat', 'title' => 'Family Flat vs Bachelor Seat: Which Is Right for You?', 'title_bn' => 'ফ্যামিলি ফ্ল্যাট নাকি ব্যাচেলর সিট?'],
			['slug' => 'choose-good-location-dhaka', 'title' => 'How to Choose a Good Location in Dhaka', 'title_bn' => 'ঢাকায় ভালো লোকেশন বেছে নেওয়ার উপায়'],
		];
		foreach ($posts as $index => $post) {
			DB::table('blog_posts')->updateOrInsert(['slug' => $post['slug']], [
				'user_id' => $admin->id,
				'title' => $post['title'],
				'title_bn' => $post['title_bn'],
				'content' => 'This original synthetic guide explains practical steps for a safer and more informed rental search.',
				'content_bn' => 'এই কৃত্রিম গাইডটি নিরাপদ ও সচেতন ভাড়া খোঁজার ব্যবহারিক ধাপ ব্যাখ্যা করে।',
				'excerpt' => 'A practical ToLetKoi development article for renters and families.',
				'featured_image' => 'demo/listings/home.svg',
				'status' => 'published',
				'published_at' => now()->subDays(5 - $index),
				'view_count' => 20 + $index,
				'updated_at' => now(),
				'created_at' => now()->subDays(6 - $index),
			]);
		}
	}

	private function seedServices(array $users, array $areas): void
	{
		$categories = [
			['slug' => 'moving-services', 'name' => 'Moving Services', 'name_bn' => 'বাসা বদল সেবা', 'icon' => 'local_shipping'],
			['slug' => 'home-repair', 'name' => 'Home Repair', 'name_bn' => 'বাড়ি মেরামত', 'icon' => 'build'],
		];
		foreach ($categories as $categoryData) {
			$category = ServiceCategory::updateOrCreate(['slug' => $categoryData['slug']], array_merge($categoryData, ['is_active' => true, 'sort_order' => 1]));
			$user = $users[0];
			DB::table('service_providers')->updateOrInsert(['slug' => 'demo-' . $categoryData['slug']], [
				'user_id' => $user->id,
				'service_category_id' => $category->id,
				'business_name' => 'Demo ' . $categoryData['name'],
				'description' => 'Synthetic development service provider for marketplace testing.',
				'phone' => $user->profile?->phone,
				'email' => $user->email,
				'address' => $areas['mirpur-model']->name . ', Bangladesh',
				'lat' => $areas['mirpur-model']->lat,
				'lng' => $areas['mirpur-model']->lng,
				'rating' => 4.7,
				'review_count' => 18,
				'job_count' => 32,
				'status' => 'active',
				'updated_at' => now(),
				'created_at' => now(),
			]);
		}
	}

	private function seedOrganizations(array $users, array $areas): void
	{
		foreach (array_slice($users['landlords'], 0, 2) as $index => $landlord) {
			$area = $areas[$index === 0 ? 'uttara' : 'dhanmondi'];
			$organizationId = DB::table('organizations')->updateOrInsert(
				['slug' => 'demo-property-management-' . ($index + 1)],
				[
					'user_id' => $landlord->id,
					'name' => 'Demo Property Management ' . ($index + 1),
					'description' => 'Synthetic property management organization for dashboard development.',
					'website' => 'https://toletkoi.test',
					'phone' => $landlord->profile?->phone,
					'email' => $landlord->email,
					'address' => $area->name . ', Bangladesh',
					'lat' => $area->lat,
					'lng' => $area->lng,
					'status' => 'active',
					'updated_at' => now(),
					'created_at' => now(),
				]
			);
			$organization = DB::table('organizations')->where('slug', 'demo-property-management-' . ($index + 1))->first();
			if ($organization) {
				$tenant = $users['tenants'][$index];
				DB::table('tenants')->updateOrInsert(['tenant_code' => 'DEMO-TENANT-' . ($index + 1)], [
					'user_id' => $tenant->id,
					'organization_id' => $organization->id,
					'emergency_contact_name' => 'Demo Emergency Contact',
					'emergency_contact_phone' => '+880180000000' . $index,
					'nid_number' => 'DEMO-NID-' . ($index + 1),
					'notes' => 'Synthetic tenant profile for development dashboards.',
					'updated_at' => now(),
					'created_at' => now(),
				]);
			}
		}
	}

	private function seedSubscription(User $user): void
	{
		$plan = SubscriptionPlan::where('slug', 'basic')->first();
		if (!$plan) {
			return;
		}
		$subscription = Subscription::updateOrCreate(['user_id' => $user->id, 'subscription_plan_id' => $plan->id], [
			'status' => 'active',
			'billing_cycle' => 'monthly',
			'amount' => $plan->price_monthly,
			'starts_at' => now()->subDays(4),
			'ends_at' => now()->addDays(26),
			'next_billing_at' => now()->addDays(26),
			'payment_method' => 'demo',
			'external_subscription_id' => 'DEMO-SUB-' . $user->id,
		]);
		PaymentTransaction::updateOrCreate(['order_id' => 'DEMO-ORDER-' . $user->id], [
			'user_id' => $user->id,
			'type' => 'subscription',
			'gateway' => 'manual',
			'amount' => $subscription->amount,
			'currency' => 'BDT',
			'status' => 'completed',
			'gateway_transaction_id' => 'DEMO-TXN-' . $user->id,
			'description' => 'Synthetic demo subscription payment',
		]);
	}
}
