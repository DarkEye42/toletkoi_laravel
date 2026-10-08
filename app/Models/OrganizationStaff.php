<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'permissions',
        'can_manage_buildings',
        'can_manage_units',
        'can_manage_tenants',
        'can_manage_maintenance',
        'can_manage_expenses',
        'can_view_reports',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'can_manage_buildings' => 'boolean',
        'can_manage_units' => 'boolean',
        'can_manage_tenants' => 'boolean',
        'can_manage_maintenance' => 'boolean',
        'can_manage_expenses' => 'boolean',
        'can_view_reports' => 'boolean',
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
