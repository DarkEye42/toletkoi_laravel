<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'type',
        'description',
        'logo',
        'banner',
        'website',
        'email',
        'phone',
        'address',
        'division_id',
        'district_id',
        'area_id',
        'lat',
        'lng',
        'registration_number',
        'tax_id',
        'is_verified',
        'is_active',
        'rating',
        'review_count',
        'property_count',
        'staff_count',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'property_count' => 'integer',
        'staff_count' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function buildings()
    {
        return $this->hasMany(PropertyBuilding::class);
    }

    public function staff()
    {
        return $this->hasMany(OrganizationStaff::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
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
}
