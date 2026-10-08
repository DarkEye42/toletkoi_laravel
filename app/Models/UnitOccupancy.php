<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOccupancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'lease_start',
        'lease_end',
        'rent_amount',
        'security_deposit_paid',
        'status',
        'move_in_date',
        'move_out_date',
        'notice_date',
        'notice_reason',
    ];

    protected $casts = [
        'lease_start' => 'date',
        'lease_end' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit_paid' => 'decimal:2',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'notice_date' => 'date',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(PropertyUnit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
