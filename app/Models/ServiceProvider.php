<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_category_id',
        'business_name',
        'business_name_bn',
        'description',
        'logo',
        'cover_image',
        'phone',
        'email',
        'website',
        'address',
        'division_id',
        'district_id',
        'area_id',
        'lat',
        'lng',
        'service_areas',
        'experience_years',
        'price_range_min',
        'price_range_max',
        'rating',
        'review_count',
        'job_count',
        'is_verified',
        'is_active',
        'is_featured',
        'response_time_hours',
        'availability',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'service_areas' => 'array',
        'experience_years' => 'integer',
        'price_range_min' => 'decimal:2',
        'price_range_max' => 'decimal:2',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'job_count' => 'integer',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'response_time_hours' => 'integer',
        'availability' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTopRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeInArea($query, $areaId)
    {
        return $query->whereJsonContains('service_areas', $areaId);
    }

    // Helpers
    public function getLogoUrl()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverImageUrl()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }
}
