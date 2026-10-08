<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_type_id',
        'property_subtype_id',
        'division_id',
        'district_id',
        'area_id',
        'title',
        'title_bn',
        'slug',
        'description',
        'description_bn',
        'listing_type',
        'price',
        'price_type',
        'bedrooms',
        'bathrooms',
        'balconies',
        'floor_level',
        'total_floors',
        'area_sqft',
        'furnishing_status',
        'address',
        'address_bn',
        'lat',
        'lng',
        'available_from',
        'is_immediately_available',
        'status',
        'rejection_reason',
        'is_featured',
        'featured_until',
        'is_promoted',
        'promoted_until',
        'contact_name',
        'contact_phone',
        'contact_email',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'balconies' => 'integer',
        'floor_level' => 'integer',
        'total_floors' => 'integer',
        'area_sqft' => 'decimal:2',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'available_from' => 'date',
        'is_immediately_available' => 'boolean',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'is_promoted' => 'boolean',
        'promoted_until' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertySubtype()
    {
        return $this->belongsTo(PropertySubtype::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function features()
    {
        return $this->hasOne(ListingFeature::class);
    }

    public function stats()
    {
        return $this->hasOne(ListingStat::class);
    }

    public function views()
    {
        return $this->hasMany(ListingView::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->where(function ($q) {
                         $q->whereNull('featured_until')
                           ->orWhere('featured_until', '>', now());
                     });
    }

    public function scopePromoted($query)
    {
        return $query->where('is_promoted', true)
                     ->where(function ($q) {
                         $q->whereNull('promoted_until')
                           ->orWhere('promoted_until', '>', now());
                     });
    }

    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    public function scopeInArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    public function scopeInDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    public function scopeInDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeWithBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', $bedrooms);
    }

    // Helper Methods

    /**
     * Get route parameters for the SEO-friendly listing URL.
     * Returns array suitable for route('listings.show', $listing->getUrlParams())
     */
    public function getUrlParams(): array
    {
        return [
            'district'     => $this->district->slug,
            'area'         => $this->area->slug,
            'propertyType' => $this->propertyType->slug,
            'propertySubtype' => $this->propertySubtype->slug,
            'listing'      => $this->slug,
        ];
    }

    /**
     * Get the full SEO-friendly URL path for this listing.
     */
    public function getUrl(): string
    {
        return route('listings.show', $this->getUrlParams());
    }

    public function getPrimaryImageUrl()
    {
        $primaryImage = $this->primaryImage;
        return $primaryImage ? asset('storage/' . $primaryImage->path) : null;
    }

    public function getAddressAttribute()
    {
        $parts = array_filter([
            $this->attributes['address'],
            $this->area->name ?? null,
            $this->district->name ?? null,
            $this->division->name ?? null,
        ]);
        return implode(', ', $parts);
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->price);
    }
}
