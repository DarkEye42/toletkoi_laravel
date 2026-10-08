<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'unit_number',
        'floor',
        'bedrooms',
        'bathrooms',
        'area_sqft',
        'rent_amount',
        'security_deposit',
        'status',
        'current_tenant_id',
    ];

    protected $casts = [
        'floor' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'area_sqft' => 'decimal:2',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    // Relationships
    public function building()
    {
        return $this->belongsTo(PropertyBuilding::class);
    }

    public function occupancies()
    {
        return $this->hasMany(UnitOccupancy::class);
    }

    public function currentTenant()
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
