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
        // Add extra columns to Spatie's roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->string('slug')->after('name')->unique();
            $table->text('description')->nullable()->after('slug');
            $table->boolean('is_switchable')->default(false)->after('description');
            $table->boolean('is_active')->default(true)->after('is_switchable');
        });

        // Create a custom user_roles table for primary role tracking
        // (Spatie's model_has_roles handles basic role assignment)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description', 'is_switchable', 'is_active']);
        });
    }
};
