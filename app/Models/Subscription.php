<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'amount',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'next_billing_at',
        'payment_method',
        'external_subscription_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_billing_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function transactions()
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Helpers
    public function isActive()
    {
        return $this->status === 'active' && $this->ends_at > now();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->ends_at <= now();
    }

    public function daysUntilExpiry()
    {
        if (!$this->isActive()) {
            return 0;
        }
        return now()->diffInDays($this->ends_at);
    }
}
