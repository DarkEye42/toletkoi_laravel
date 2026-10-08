<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'admin_id',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Relationships
    public function wallet()
    {
        return $this->belongsTo(CreditWallet::class, 'wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeEarnings($query)
    {
        return $query->where('type', 'earn');
    }

    public function scopeSpending($query)
    {
        return $query->where('type', 'spend');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }
}
