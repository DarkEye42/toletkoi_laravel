<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_spent',
        'total_purchased',
        'total_refunded',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_purchased' => 'decimal:2',
        'total_refunded' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class, 'wallet_id');
    }

    // Helper Methods
    public function hasEnoughCredits($amount)
    {
        return $this->balance >= $amount;
    }

    public function credit($amount, $type, $description, $referenceType = null, $referenceId = null)
    {
        $this->balance += $amount;

        if ($type === 'earn' || $type === 'purchase' || $type === 'refund' || $type === 'bonus') {
            $this->total_earned += $amount;
        }

        $this->save();

        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function debit($amount, $type, $description, $referenceType = null, $referenceId = null)
    {
        if (!$this->hasEnoughCredits($amount)) {
            throw new \InsufficientCreditException('Insufficient credits');
        }

        $this->balance -= $amount;

        if ($type === 'spend') {
            $this->total_spent += $amount;
        }

        $this->save();

        return $this->transactions()->create([
            'type' => $type,
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }
}
