<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'tenant_id',
        'landlord_id',
        'application_id',
        'rent_amount',
        'security_deposit',
        'lease_start',
        'lease_end',
        'payment_day',
        'late_fee_amount',
        'late_fee_grace_days',
        'terms_conditions',
        'special_conditions',
        'document_path',
        'status',
        'signed_by_tenant_at',
        'signed_by_landlord_at',
        'terminated_at',
        'termination_reason',
        'notice_date',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'lease_start' => 'date',
        'lease_end' => 'date',
        'payment_day' => 'integer',
        'late_fee_amount' => 'decimal:2',
        'late_fee_grace_days' => 'integer',
        'signed_by_tenant_at' => 'datetime',
        'signed_by_landlord_at' => 'datetime',
        'terminated_at' => 'datetime',
        'notice_date' => 'date',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function application()
    {
        return $this->belongsTo(RentalApplication::class, 'application_id');
    }

    public function rentPayments()
    {
        return $this->hasMany(RentPayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('lease_end', '<', now());
    }

    // Helpers
    public function isActive()
    {
        return $this->status === 'active' && $this->lease_end > now();
    }

    public function monthsRemaining()
    {
        return now()->diffInMonths($this->lease_end);
    }
}
