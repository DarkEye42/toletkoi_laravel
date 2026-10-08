<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The `areas` table is already created (with an identical column set) by
     * 2026_09_15_000001_create_geography_tables. This migration previously
     * called Schema::create() unconditionally, which made every fresh
     * migration run fail with "table \"areas\" already exists".
     *
     * It is kept as an idempotent no-op so existing databases that have this
     * migration recorded in the `migrations` table stay in sync, while fresh
     * runs no longer attempt to create the table twice.
     */
    public function up(): void
    {
        if (Schema::hasTable('areas')) {
            return;
        }

        Schema::create('areas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('district_id')
                ->constrained('districts')
                ->cascadeOnDelete();

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
     * The table is owned by the geography migration, so this migration must
     * not drop it on rollback.
     */
    public function down(): void
    {
        //
    }
};
