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
        // Divisions table - Bangladesh administrative divisions
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // English name
            $table->string('name_bn'); // Bangla name
            $table->string('slug')->unique();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('property_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Districts table - 64 districts within divisions
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug')->unique();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('property_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('division_id');
        });

        // Areas table - neighborhoods/areas within districts
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug')->unique();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('property_count')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
