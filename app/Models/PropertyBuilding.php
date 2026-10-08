<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyBuilding extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'address',
        'division_id',
        'district_id',
        'area_id',
        'lat',
        'lng',
        'total_floors',
        'total_units',
        'year_built',
        'has_elevator',
        'has_parking',
        'has_generator',
        'has_security',
        'has_cctv',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'total_floors' => 'integer',
        'total_units' => 'integer',
        'year_built' => 'integer',
        'has_elevator' => 'boolean',
        'has_parking' => 'boolean',
        'has_generator' => 'boolean',
        'has_security' => 'boolean',
        'has_cctv' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function units()
    {
        return $this->hasMany(PropertyUnit::class);
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
}
