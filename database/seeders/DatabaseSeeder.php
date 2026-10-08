<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PropertyTypeSeeder::class,
            GeographySeeder::class,
            CreditPackageSeeder::class,
            SubscriptionPlanSeeder::class,
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            ToLetKoiDemoSeeder::class,
        ]);
    }
}
