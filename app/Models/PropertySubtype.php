<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySubtype extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_type_id',
        'name',
        'name_bn',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
