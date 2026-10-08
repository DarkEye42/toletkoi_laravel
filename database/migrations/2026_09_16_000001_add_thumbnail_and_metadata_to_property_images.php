<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Listing images are converted to WebP and a downscaled thumbnail is generated
 * for listing cards and dashboard tables. The thumbnail path needs its own
 * column so views never have to derive it from the full-size path.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_images', function (Blueprint $table) {
            if (!Schema::hasColumn('property_images', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('path');
            }

            if (!Schema::hasColumn('property_images', 'width')) {
                $table->unsignedInteger('width')->nullable()->after('is_primary');
            }

            if (!Schema::hasColumn('property_images', 'height')) {
                $table->unsignedInteger('height')->nullable()->after('width');
            }

            if (!Schema::hasColumn('property_images', 'size')) {
                $table->unsignedInteger('size')->nullable()->after('height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_images', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_path', 'width', 'height', 'size']);
        });
    }
};
