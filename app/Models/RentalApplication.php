<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'status',
        'proposed_rent',
        'proposed_lease_start',
        'proposed_lease_end',
        'message',
        'employment_status',
        'employer_name',
        'monthly_income',
        'references',
        'documents',
        'landlord_notes',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'proposed_rent' => 'decimal:2',
        'proposed_lease_start' => 'date',
        'proposed_lease_end' => 'date',
        'monthly_income' => 'decimal:2',
        'references' => 'array',
        'documents' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
