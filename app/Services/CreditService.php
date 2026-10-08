<?php

namespace App\Services;

use App\Models\CreditWallet;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Cost per contact unlock in credits.
     */
    public function getUnlockCost(): int
    {
        return (int) setting('credits.contact_unlock', 50);
    }

    /**
     * Check if the user has an active subscription with unlock allowance remaining.
     */
    public function hasSubscriptionAllowance(User $user): bool
    {
        $subscription = $user->activeSubscription;

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        $unlocksUsed = \App\Models\ContactUnlock::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        return $unlocksUsed < $subscription->plan->contact_unlocks_per_month;
    }

    /**
     * Get or create wallet for user.
     */
    public function getWallet(User $user): CreditWallet
    {
        return CreditWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    /**
     * Get current balance for a user.
     */
    public function getBalance(User $user): int
    {
        $wallet = $this->getWallet($user);
        return (int) ($wallet->balance ?? 0);
    }

    /**
     * Deduct credits for a contact unlock. Returns true on success, throws on failure.
     */
    public function deductForUnlock(User $user, Listing $listing): CreditTransaction
    {
        $unlockCost = $this->getUnlockCost();

        // Subscription users don't pay credits
        if ($this->hasSubscriptionAllowance($user)) {
            $this->createUnlockRecord($user, $listing, 0);
            $wallet = $this->getWallet($user);
            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'spend',
                'amount' => 0,
                'balance_after' => $wallet->balance,
                'description' => "Contact unlock for listing #{$listing->id} (subscription)",
                'reference_type' => Listing::class,
                'reference_id' => $listing->id,
            ]);
        }

        $this->getWallet($user);

        return DB::transaction(function () use ($unlockCost, $user, $listing) {
            // Re-fetch the wallet inside the transaction and lock the row so two
            // concurrent unlocks can't both pass the balance check.
            $wallet = CreditWallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($wallet->balance < $unlockCost) {
                throw new \App\Exceptions\InsufficientCreditException(
                    $unlockCost,
                    $wallet->balance
                );
            }

            $wallet->decrement('balance', $unlockCost);

            $transaction = CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'spend',
                'amount' => $unlockCost,
                'balance_after' => $wallet->fresh()->balance,
                'description' => "Contact unlock for listing #{$listing->id}",
                'reference_type' => Listing::class,
                'reference_id' => $listing->id,
            ]);

            $this->createUnlockRecord($user, $listing, $unlockCost);

            return $transaction;
        });
    }

    /**
     * Add credits to a user's wallet (e.g., after purchase).
     */
    public function addCredits(User $user, int $amount, string $description, $referenceType = null, $referenceId = null): CreditTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be positive.');
        }

        $wallet = $this->getWallet($user);

        return DB::transaction(function () use ($wallet, $amount, $user, $description, $referenceType, $referenceId) {
            $wallet->increment('balance', $amount);

            return CreditTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'earn',
                'amount' => $amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * Get credit transaction history for a user.
     */
    public function getTransactionHistory(User $user, int $perPage = 15)
    {
        return CreditTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Record a contact unlock entry.
     */
    protected function createUnlockRecord(User $user, Listing $listing, int $creditsUsed): \App\Models\ContactUnlock
    {
        return \App\Models\ContactUnlock::create([
            'user_id' => $user->id,
            'lead_id' => $listing->id,
            'credits_used' => $creditsUsed,
            'contact_type' => 'phone',
        ]);
    }
}
