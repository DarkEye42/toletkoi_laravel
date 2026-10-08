<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingService
{
    public function __construct(
        protected ImageService $imageService
    ) {
    }

    /**
     * Boolean feature flags stored on the listing_features table.
     */
    protected const FEATURE_KEYS = [
        'has_parking', 'has_lift', 'has_generator', 'has_gas',
        'electricity_included', 'water_included', 'has_security',
        'has_cctv', 'has_wifi', 'is_furnished', 'has_ac',
        'has_washing_machine', 'shared_kitchen', 'attached_bathroom',
    ];

    /**
     * Create a new listing with features and images.
     */
    public function createListing(User $user, array $data): Listing
    {
        return DB::transaction(function () use ($user, $data) {
            $listing = Listing::create($this->buildAttributes($data, $user, true));

            $this->createFeatureRecord($listing, $data);

            if (!empty($data['images']) && is_array($data['images'])) {
                $this->uploadImages($listing, $data['images']);
            }

            return $listing;
        });
    }

    /**
     * Update a listing with features and images.
     */
    public function updateListing(Listing $listing, array $data): Listing
    {
        return DB::transaction(function () use ($listing, $data) {
            $listing->update($this->buildAttributes($data, null, false));

            $this->updateFeatureRecord($listing, $data);

            if (!empty($data['images']) && is_array($data['images'])) {
                $this->uploadImages($listing, $data['images']);
            }

            return $listing;
        });
    }

    /**
     * Build the shared listing attribute array used by create and update.
     *
     * When $isCreate is true the contact fields fall back to the owner's
     * name/email and a unique slug plus pending status are generated.
     */
    protected function buildAttributes(array $data, ?User $user, bool $isCreate): array
    {
        $attributes = [
            'property_type_id'        => $data['property_type_id'],
            'property_subtype_id'     => $data['subtype_id'] ?? null,
            'division_id'             => $data['division_id'],
            'district_id'             => $data['district_id'],
            'area_id'                 => $data['area_id'] ?? null,
            'title'                   => $data['title'],
            'title_bn'                => $data['title_bn'] ?? null,
            'description'             => $data['description'],
            'description_bn'          => $data['description_bn'] ?? null,
            'listing_type'            => $data['listing_type'] ?? 'rent',
            'price'                   => $data['price'],
            'price_type'              => $data['price_type'] ?? 'monthly',
            'bedrooms'                => $data['bedrooms'] ?? 0,
            'bathrooms'               => $data['bathrooms'] ?? 0,
            'balconies'               => $data['balconies'] ?? 0,
            'floor_level'             => $data['floor_level'] ?? null,
            'total_floors'            => $data['total_floors'] ?? null,
            'area_sqft'               => $data['area_sqft'] ?? null,
            'furnishing_status'       => $data['furnishing_status'] ?? 'unfurnished',
            'address'                 => $data['address'],
            'address_bn'              => $data['address_bn'] ?? null,
            'lat'                     => $data['lat'] ?? null,
            'lng'                     => $data['lng'] ?? null,
            'contact_name'            => $data['contact_name'] ?? ($user?->name),
            'contact_phone'           => $data['contact_phone'] ?? null,
            'contact_email'           => $data['contact_email'] ?? ($user?->email),
            'available_from'          => $data['available_from'] ?? null,
            'is_immediately_available' => $data['is_immediately_available'] ?? true,
        ];

        if ($isCreate && $user) {
            $attributes['user_id'] = $user->id;
            $attributes['slug'] = $this->generateUniqueSlug($data['title']);
            $attributes['status'] = 'pending';
        }

        return $attributes;
    }

    /**
     * Delete a listing and its associated files.
     */
    public function deleteListing(Listing $listing): bool
    {
        // Collect both the full-size image and its thumbnail so no orphaned
        // files are left behind on the public disk.
        $paths = $listing->images
            ->flatMap(fn ($image) => array_filter([$image->path, $image->thumbnail_path]))
            ->values()
            ->all();

        DB::transaction(function () use ($listing) {
            $listing->images()->delete();
            $listing->features()->delete();
            $listing->stats()->delete();
            $listing->views()->delete();
            $listing->delete();
        });

        // Only remove the files once the database rows are committed, so a
        // rolled-back transaction never leaves orphaned rows pointing at
        // deleted files.
        $this->deleteStoredFiles($paths);

        return true;
    }

    /**
     * Delete stored files, logging (but not throwing on) failures so the
     * caller can retry the cleanup later.
     */
    protected function deleteStoredFiles(array $paths): void
    {
        foreach ($paths as $path) {
            try {
                if (!Storage::disk('public')->exists($path)) {
                    continue;
                }

                if (!Storage::disk('public')->delete($path)) {
                    \Illuminate\Support\Facades\Log::warning('Failed to delete listing image file.', ['path' => $path]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Error deleting listing image file.', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Duplicate a listing as a new pending listing.
     */
    public function duplicateListing(Listing $original, User $user): Listing
    {
        $data = $original->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
        $data['title'] = $data['title'] . ' (Copy)';
        $data['subtype_id'] = $data['property_subtype_id'];

        $imageRecords = $original->images()
            ->orderBy('sort_order')
            ->get(['path', 'original_name', 'is_primary', 'sort_order']);

        $listing = $this->createListing($user, $data);

        $sortOrder = 0;

        foreach ($imageRecords as $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                $contents = Storage::disk('public')->get($img->path);
                $filename = basename($img->path);
                $newPath = 'listings/' . $listing->id . '/' . $filename;
                Storage::disk('public')->put($newPath, $contents);
                PropertyImage::create([
                    'listing_id'    => $listing->id,
                    'path'          => $newPath,
                    'original_name' => $img->original_name,
                    'sort_order'    => $sortOrder++,
                    'is_primary'    => $img->is_primary,
                ]);
            }
        }

        return $listing;
    }

    /**
     * Record a view using the listing_stats table.
     */
    public function recordView(Listing $listing): void
    {
        $stats = $listing->stats()->firstOrCreate(
            ['listing_id' => $listing->id],
            ['view_count' => 0, 'last_viewed_at' => now()]
        );

        $stats->increment('view_count');
        $stats->update(['last_viewed_at' => now()]);
    }

    /**
     * Create feature flags record for a new listing.
     */
    protected function createFeatureRecord(Listing $listing, array $data): void
    {
        ListingFeature::create(
            array_merge(['listing_id' => $listing->id], $this->mapFeatureValues($data, false))
        );
    }

    /**
     * Update feature flags record for an existing listing.
     */
    protected function updateFeatureRecord(Listing $listing, array $data): void
    {
        $featuresData = $this->mapFeatureValues($data, true);

        if (!empty($featuresData)) {
            $listing->features()->updateOrCreate(
                ['listing_id' => $listing->id],
                $featuresData
            );
        }
    }

    /**
     * Map the incoming request data onto the listing_features boolean columns.
     *
     * When $skipMissing is true only keys present in the payload are returned
     * (used for partial updates); otherwise every known feature key is mapped.
     */
    protected function mapFeatureValues(array $data, bool $skipMissing): array
    {
        $featuresData = [];

        foreach (self::FEATURE_KEYS as $key) {
            if ($skipMissing && !array_key_exists($key, $data)) {
                continue;
            }

            $featuresData[$key] = !empty($data[$key]);
        }

        return $featuresData;
    }

    /**
     * Upload images for a listing.
     *
     * Each upload is validated, re-encoded to WebP and given a thumbnail by
     * ImageService before being recorded against the listing.
     *
     * @param  array<int, UploadedFile>  $images
     */
    protected function uploadImages(Listing $listing, array $images): void
    {
        $existingCount = $listing->images()->count();

        foreach ($images as $index => $image) {
            if (!$image instanceof UploadedFile) {
                throw new \InvalidArgumentException(sprintf(
                    'Image at index %s must be an instance of %s, %s given.',
                    $index,
                    UploadedFile::class,
                    is_object($image) ? get_class($image) : gettype($image)
                ));
            }

            $processed = $this->imageService->processListingImage(
                $image,
                'listings/' . $listing->id
            );

            PropertyImage::create([
                'listing_id'     => $listing->id,
                'path'           => $processed['path'],
                'thumbnail_path' => $processed['thumbnail_path'],
                'original_name'  => $image->getClientOriginalName(),
                'sort_order'     => $existingCount + $index,
                'is_primary'     => ($existingCount === 0 && $index === 0),
                'width'          => $processed['width'],
                'height'         => $processed['height'],
                'size'           => $processed['size'],
            ]);
        }
    }

    /**
     * Generate a unique slug from title.
     */
    protected function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (Listing::withoutGlobalScopes()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
