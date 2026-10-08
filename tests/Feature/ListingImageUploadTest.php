<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\Listing;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\ImageService;
use App\Services\ListingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Listing image uploads must be validated, converted to WebP and given a
 * thumbnail. Before this was wired up, validated() silently dropped the
 * uploaded files and no images were ever persisted.
 */
class ListingImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Build a genuine image file that GD can decode.
     */
    protected function fakeImage(int $width = 2400, int $height = 1600, string $name = 'photo.jpg'): UploadedFile
    {
        $image = imagecreatetruecolor($width, $height);
        $colour = imagecolorallocate($image, 30, 144, 255);
        imagefilledrectangle($image, 0, 0, $width, $height, $colour);

        $path = tempnam(sys_get_temp_dir(), 'tlt') . '.jpg';
        imagejpeg($image, $path, 90);
        imagedestroy($image);

        return new UploadedFile($path, $name, 'image/jpeg', null, true);
    }

    public function test_image_service_converts_to_webp_and_bounds_dimensions(): void
    {
        $result = app(ImageService::class)->processListingImage($this->fakeImage(), 'listings/1');

        $this->assertStringEndsWith('.webp', $result['path']);
        $this->assertStringEndsWith('.webp', $result['thumbnail_path']);

        Storage::disk('public')->assertExists($result['path']);
        Storage::disk('public')->assertExists($result['thumbnail_path']);

        $full = getimagesizefromstring(Storage::disk('public')->get($result['path']));
        $this->assertSame('image/webp', $full['mime']);
        $this->assertLessThanOrEqual(ImageService::MAX_EDGE, max($full[0], $full[1]));

        $thumb = getimagesizefromstring(Storage::disk('public')->get($result['thumbnail_path']));
        $this->assertLessThanOrEqual(ImageService::THUMB_EDGE, max($thumb[0], $thumb[1]));
    }

    public function test_image_service_rejects_non_image_payload(): void
    {
        $fake = UploadedFile::fake()->create('payload.jpg', 10, 'image/jpeg');

        $this->expectException(\RuntimeException::class);

        app(ImageService::class)->processListingImage($fake, 'listings/1');
    }

    /**
     * Create a minimal valid listing for the given user.
     */
    protected function makeListing(User $user): Listing
    {
        $type = PropertyType::create(['name' => 'Apartment', 'name_bn' => 'অ্যাপার্টমেন্ট', 'slug' => 'apartment', 'icon' => 'apartment']);
        $division = Division::create(['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'slug' => 'dhaka']);
        $district = District::create(['name' => 'Gulshan', 'name_bn' => 'গুলশান', 'slug' => 'gulshan', 'division_id' => $division->id]);
        $area = Area::create(['name' => 'Gulshan 1', 'name_bn' => 'গুলশান ১', 'slug' => 'gulshan-1', 'district_id' => $district->id]);

        return Listing::create([
            'user_id' => $user->id,
            'property_type_id' => $type->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'area_id' => $area->id,
            'title' => 'Image Upload Test Listing',
            'slug' => 'image-upload-test-listing',
            'description' => 'A listing used to test image handling.',
            'listing_type' => 'rent',
            'price' => 25000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'address' => '123 Test Road',
            'status' => 'active',
        ]);
    }

    public function test_listing_service_stores_image_records_with_thumbnails(): void
    {
        $user = User::factory()->create();
        $listing = $this->makeListing($user);

        $service = app(ListingService::class);
        $method = new \ReflectionMethod(ListingService::class, 'uploadImages');
        $method->setAccessible(true);
        $method->invoke($service, $listing, [$this->fakeImage()]);

        $image = PropertyImage::where('listing_id', $listing->id)->first();

        $this->assertNotNull($image, 'An image record should have been created.');
        $this->assertNotNull($image->thumbnail_path);
        $this->assertSame(2400, $image->width);
        $this->assertSame(1600, $image->height);
        $this->assertTrue($image->is_primary, 'The first image should be primary.');
        Storage::disk('public')->assertExists($image->path);
        Storage::disk('public')->assertExists($image->thumbnail_path);
    }

    public function test_deleting_a_listing_removes_image_and_thumbnail_files(): void
    {
        $user = User::factory()->create();
        $listing = $this->makeListing($user);

        $service = app(ListingService::class);
        $upload = new \ReflectionMethod(ListingService::class, 'uploadImages');
        $upload->setAccessible(true);
        $upload->invoke($service, $listing, [$this->fakeImage()]);

        $image = PropertyImage::where('listing_id', $listing->id)->firstOrFail();
        $fullPath = $image->path;
        $thumbPath = $image->thumbnail_path;

        $service->deleteListing($listing->fresh());

        Storage::disk('public')->assertMissing($fullPath);
        Storage::disk('public')->assertMissing($thumbPath);
        $this->assertDatabaseMissing('property_images', ['id' => $image->id]);
    }
}
