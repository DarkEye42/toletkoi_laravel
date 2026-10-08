<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The listings.status enum originally allowed only
 * ['pending', 'active', 'inactive', 'rented', 'sold'].
 *
 * Admin moderation needs to mark a submission as 'rejected' (with a
 * rejection_reason shown to the owner). Without 'rejected' in the enum the
 * UPDATE fails with a CHECK constraint violation.
 */
return new class extends Migration
{
    protected const ALLOWED = ['pending', 'active', 'inactive', 'rented', 'sold', 'rejected'];

    public function up(): void
    {
        // SQLite cannot ALTER an enum column in place, so the schema is rebuilt
        // for that driver; MySQL/MariaDB apply a MODIFY.
        if (!Schema::hasTable('listings')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            // The column must exist before any moderation UPDATE, and it has to
            // be added before the constraint rebuild copies rows across.
            if (!Schema::hasColumn('listings', 'rejection_reason')) {
                Schema::table('listings', function (Blueprint $table) {
                    $table->text('rejection_reason')->nullable();
                });
            }

            $this->rebuildSqliteListingsStatus();

            return;
        }

        $list = "'" . implode("','", self::ALLOWED) . "'";

        DB::statement("ALTER TABLE listings MODIFY status ENUM({$list}) NOT NULL DEFAULT 'pending'");

        // The column that stores the moderation reason never existed on
        // `listings`, so rejection could not explain itself to the owner.
        if (!Schema::hasColumn('listings', 'rejection_reason')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->text('rejection_reason')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('listings')) {
            return;
        }

        if (Schema::hasColumn('listings', 'rejection_reason')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('rejection_reason');
            });
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $list = "'pending','active','inactive','rented','sold'";

        DB::statement("ALTER TABLE listings MODIFY status ENUM({$list}) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Drop the SQLite CHECK constraint on `listings.status`.
     *
     * SQLite cannot ALTER a constraint in place, and renaming the table to
     * recreate it would break every foreign key that references `listings`
     * (for example listing_stats.listing_id), because SQLite rewrites those
     * references to follow the rename.
     *
     * Instead we rewrite `sqlite_master` directly, which is the supported way
     * to relax a CHECK constraint without touching the table or its FKs.
     * Writes to sqlite_master are permitted while `writable_schema` is on.
     */
    protected function rebuildSqliteListingsStatus(): void
    {
        $table = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='listings'");

        if (!$table) {
            return;
        }

        $original = (string) $table->sql;

        // Already widened, or shaped differently - nothing to do.
        if (str_contains($original, "'rejected'")) {
            return;
        }

        $widened = preg_replace(
            "/check\s*\(\s*[`\"']?status[`\"']?\s+in\s*\([^)]*\)\s*\)/i",
            "check (status in ('pending','active','inactive','rented','sold','rejected'))",
            $original,
            1
        );

        if ($widened === null || $widened === $original) {
            return;
        }

        DB::statement('PRAGMA writable_schema = ON');
        DB::statement(
            "UPDATE sqlite_master SET sql = ? WHERE type = 'table' AND name = 'listings'",
            [$widened]
        );
        DB::statement('PRAGMA writable_schema = OFF');

        // Force SQLite to reload the schema so the change takes effect.
        DB::statement('PRAGMA schema_version = ' . (DB::selectOne('PRAGMA schema_version')->schema_version + 1));
    }
};
