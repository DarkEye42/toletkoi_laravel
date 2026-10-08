<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'has_parking',
        'has_lift',
        'has_generator',
        'has_gas',
        'electricity_included',
        'water_included',
        'has_security',
        'has_cctv',
        'has_wifi',
        'is_furnished',
        'has_ac',
        'has_washing_machine',
        'shared_kitchen',
        'attached_bathroom',
    ];

    protected $casts = [
        'has_parking' => 'boolean',
        'has_lift' => 'boolean',
        'has_generator' => 'boolean',
        'has_gas' => 'boolean',
        'electricity_included' => 'boolean',
        'water_included' => 'boolean',
        'has_security' => 'boolean',
        'has_cctv' => 'boolean',
        'has_wifi' => 'boolean',
        'is_furnished' => 'boolean',
        'has_ac' => 'boolean',
        'has_washing_machine' => 'boolean',
        'shared_kitchen' => 'boolean',
        'attached_bathroom' => 'boolean',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Helper to get all enabled features
    public function getEnabledFeatures()
    {
        $features = [];
        foreach ($this->getAttributes() as $key => $value) {
            if (str_starts_with($key, 'has_') && $value) {
                $features[] = str_replace('has_', '', $key);
            }
        }
        return $features;
    }
}
