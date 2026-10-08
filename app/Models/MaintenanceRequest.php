<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'reported_by',
        'organization_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'estimated_cost',
        'actual_cost',
        'vendor_name',
        'vendor_phone',
        'scheduled_date',
        'completed_date',
        'images',
        'notes',
        'tenant_satisfaction_rating',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'images' => 'array',
        'tenant_satisfaction_rating' => 'integer',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(PropertyUnit::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignments()
    {
        return $this->hasMany(MaintenanceAssignment::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['new', 'in_progress']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
