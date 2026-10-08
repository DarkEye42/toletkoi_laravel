<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Favorites - User-Listing favorites
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'listing_id']);
            $table->index('user_id');
            $table->index('listing_id');
        });

        // Saved Searches - Saved search criteria
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('criteria'); // Search criteria as JSON
            $table->boolean('notify_new_matches')->default(true);
            $table->integer('match_count')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        // Credit Wallets - Credit balance per user
        Schema::create('credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('total_purchased', 12, 2)->default(0);
            $table->decimal('total_refunded', 12, 2)->default(0);
            $table->timestamps();

            $table->unique('user_id');
        });

        // Credit Transactions - Full ledger
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('credit_wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['purchase', 'earn', 'spend', 'refund', 'bonus', 'admin_adjustment']);
            $table->decimal('amount', 12, 2); // Positive = credit, negative = debit
            $table->decimal('balance_after', 12, 2);
            $table->string('description');
            $table->string('reference_type')->nullable(); // Polymorphic: payment, listing, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('admin_id')->nullable(); // For admin adjustments
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('wallet_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['reference_type', 'reference_id']);
        });

        // Credit Packages - Predefined credit bundles
        Schema::create('credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->integer('credits');
            $table->decimal('price', 10, 2);
            $table->decimal('bonus_credits', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Subscription Plans - Subscription tier definitions
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2)->nullable();
            $table->integer('max_listings')->default(1);
            $table->integer('max_images_per_listing')->default(5);
            $table->boolean('is_featured_included')->default(false);
            $table->boolean('is_promoted_included')->default(false);
            $table->boolean('is_analytics_included')->default(false);
            $table->boolean('is_priority_support')->default(false);
            $table->json('features')->nullable(); // Additional features as JSON
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Subscriptions - Active user subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'cancelled', 'expired', 'suspended'])->default('active');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('external_subscription_id')->nullable(); // Gateway subscription ID
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('ends_at');
        });

        // Subscription Transactions - Subscription payment history
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['subscription', 'renewal', 'upgrade', 'downgrade', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable(); // Gateway transaction ID
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('subscription_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        // Payment Transactions - Unified payment records
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique(); // Unique order ID
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // credit_purchase, subscription, order_home, etc.
            $table->string('gateway'); // ssl_commerz, bkash, nagad, rocket, manual
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('subscription_transactions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('credit_packages');
        Schema::dropIfExists('credit_transactions');
        Schema::dropIfExists('credit_wallets');
        Schema::dropIfExists('saved_searches');
        Schema::dropIfExists('favorites');
    }
};
