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
        // NOTE: The `roles` and `model_has_roles` tables are created by Spatie Permission
        // (2026_09_14_222952_create_permission_tables migration).
        // We add extra columns to `roles` in a separate migration below.

        // Role Change Logs - Audit trail for role switches
        Schema::create('role_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('old_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('new_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->string('action'); // 'switch', 'assign', 'remove'
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        // User Profiles - Extended user information
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('nid_number')->nullable(); // National ID
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('address_bn')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('division_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('area_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('nid_verified_at')->nullable();
            $table->timestamp('photo_verified_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // User Preferences - Notification and display settings
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->string('language')->default('en'); // 'en' or 'bn'
            $table->string('currency')->default('BDT');
            $table->string('theme')->default('light'); // 'light' or 'dark'
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('role_change_logs');
        // NOTE: roles and model_has_roles tables are managed by Spatie Permission migration
    }
};
