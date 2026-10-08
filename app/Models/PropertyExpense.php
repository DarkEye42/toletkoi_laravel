<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_unit_id',
        'organization_id',
        'category',
        'description',
        'amount',
        'date',
        'recurring',
        'recurring_frequency',
        'receipt_path',
        'vendor_name',
        'notes',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'recurring' => 'boolean',
    ];

    // Relationships
    public function propertyUnit()
    {
        return $this->belongsTo(PropertyUnit::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}
