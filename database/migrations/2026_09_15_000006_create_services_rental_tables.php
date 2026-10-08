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
        // Verification Types - Type definitions
        Schema::create('verification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Verifications - Verification requests
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verification_type_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('verified_by')->nullable(); // Staff user ID
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });

        // Verification Documents - Document uploads
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();

            $table->index('verification_id');
        });

        // Leads - Property inquiries
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['new', 'contacted', 'converted', 'expired', 'cancelled'])->default('new');
            $table->text('message')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index('listing_id');
            $table->index('user_id');
            $table->index('owner_id');
            $table->index('status');
        });

        // Contact Unlocks - Contact reveal records
        Schema::create('contact_unlocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('credits_used', 10, 2)->default(0);
            $table->string('contact_type'); // phone, email, full
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('user_id');
        });

        // KoiFind Requests - Assisted search requests
        Schema::create('koifind_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->json('criteria'); // Search criteria
            $table->text('notes')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->string('preferred_area')->nullable();
            $table->date('preferred_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('agent_id');
            $table->index('status');
        });

        // KoiFind Candidates - Agent-found properties
        Schema::create('koifind_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koifind_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['pending', 'shortlisted', 'selected', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index('koifind_request_id');
        });

        // KoiFind Shortlists - Curated shortlists
        Schema::create('koifind_shortlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koifind_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('koifind_request_id');
        });

        // Rental Applications - Tenant applications
        Schema::create('rental_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->date('move_in_date')->nullable();
            $table->text('message')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('listing_id');
            $table->index('user_id');
            $table->index('owner_id');
            $table->index('status');
        });

        // Rental Agreements - Formal rental agreements
        Schema::create('rental_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('terms')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();

            $table->index('listing_id');
            $table->index('tenant_id');
            $table->index('landlord_id');
            $table->index('status');
        });

        // Rent Payments - Rent collection tracking
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_agreement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'late', 'partial'])->default('pending');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('rental_agreement_id');
            $table->index('tenant_id');
            $table->index('landlord_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
        Schema::dropIfExists('rental_agreements');
        Schema::dropIfExists('rental_applications');
        Schema::dropIfExists('koifind_shortlists');
        Schema::dropIfExists('koifind_candidates');
        Schema::dropIfExists('koifind_requests');
        Schema::dropIfExists('contact_unlocks');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('verification_documents');
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('verification_types');
    }
};
