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
        // Property Types - Property categories
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Icon class or SVG path
            $table->text('description')->nullable();
            $table->integer('listing_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Property Subtypes - Subcategories within types
        Schema::create('property_subtypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('property_type_id');
        });

        // Listings - Property listings (main table)
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_subtype_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();

            // Basic info
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_bn')->nullable();

            // Property details
            $table->enum('listing_type', ['rent', 'sale'])->default('rent');
            $table->decimal('price', 12, 2);
            $table->enum('price_type', ['monthly', 'weekly', 'daily', 'negotiable'])->default('monthly');
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('balconies')->default(0);
            $table->integer('floor_level')->nullable();
            $table->integer('total_floors')->nullable();
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->enum('furnishing_status', ['unfurnished', 'semi_furnished', 'furnished'])->default('unfurnished');

            // Location
            $table->string('address');
            $table->string('address_bn')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            // Availability
            $table->date('available_from')->nullable();
            $table->boolean('is_immediately_available')->default(true);

            // Status
            $table->enum('status', ['pending', 'active', 'inactive', 'rented', 'sold'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->boolean('is_promoted')->default(false);
            $table->timestamp('promoted_until')->nullable();

            // Contact info
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Soft deletes
            $table->softDeletes();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('property_type_id');
            $table->index('division_id');
            $table->index('district_id');
            $table->index('area_id');
            $table->index('status');
            $table->index('listing_type');
            $table->index('price');
            $table->index('bedrooms');
            $table->index('is_featured');
            $table->index('created_at');
        });

        // Property Images - Separate images table
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('listing_id');
        });

        // Listing Features - Boolean feature flags
        Schema::create('listing_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_lift')->default(false);
            $table->boolean('has_generator')->default(false);
            $table->boolean('has_gas')->default(false);
            $table->boolean('electricity_included')->default(false);
            $table->boolean('water_included')->default(false);
            $table->boolean('has_security')->default(false);
            $table->boolean('has_cctv')->default(false);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('is_furnished')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('has_washing_machine')->default(false);
            $table->boolean('shared_kitchen')->default(false);
            $table->boolean('attached_bathroom')->default(false);
            $table->timestamps();

            $table->unique('listing_id');
        });

        // Listing Stats - Denormalized stats
        Schema::create('listing_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('contact_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamps();

            $table->unique('listing_id');
        });

        // Listing Views - View tracking
        Schema::create('listing_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();

            $table->index('listing_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_views');
        Schema::dropIfExists('listing_stats');
        Schema::dropIfExists('listing_features');
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('property_subtypes');
        Schema::dropIfExists('property_types');
    }
};
