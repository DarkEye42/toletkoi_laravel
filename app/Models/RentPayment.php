<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'user_id',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'transaction_id',
        'late_fee',
        'late_fee_waived',
        'late_fee_waived_reason',
        'receipt_path',
        'notes',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'late_fee' => 'decimal:2',
        'late_fee_waived' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function agreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->orWhere(function ($q) {
                         $q->where('status', 'pending')
                           ->where('due_date', '<', now());
                     });
    }

    // Helpers
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function daysUntilDue()
    {
        if ($this->status !== 'pending') {
            return null;
        }
        return max(0, now()->diffInDays($this->due_date, false));
    }
}
