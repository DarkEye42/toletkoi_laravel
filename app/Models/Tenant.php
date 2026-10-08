<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'phone',
        'email',
        'nid_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'status',
        'credit_score',
        'reliability_rating',
        'total_occupancies',
        'total_payments_on_time',
        'total_payments_late',
        'total_violations',
        'is_active',
    ];

    protected $casts = [
        'credit_score' => 'integer',
        'reliability_rating' => 'decimal:2',
        'total_occupancies' => 'integer',
        'total_payments_on_time' => 'integer',
        'total_payments_late' => 'integer',
        'total_violations' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function occupancies()
    {
        return $this->hasMany(UnitOccupancy::class);
    }

    public function notes()
    {
        return $this->hasMany(TenantNote::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'reported_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
