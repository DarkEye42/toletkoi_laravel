<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditPackage;

class CreditPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'name_bn' => 'স্টার্টার',
                'credits' => 100,
                'price' => 499,
                'bonus_credits' => 0,
                'description' => 'Perfect for getting started with a few listings',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'name_bn' => 'বেসিক',
                'credits' => 500,
                'price' => 1999,
                'bonus_credits' => 50,
                'description' => 'Great for regular landlords',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional',
                'name_bn' => 'প্রফেশনাল',
                'credits' => 1000,
                'price' => 3499,
                'bonus_credits' => 150,
                'description' => 'For serious property managers',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'name_bn' => 'বিজনেস',
                'credits' => 2500,
                'price' => 7499,
                'bonus_credits' => 500,
                'description' => 'Best value for agencies and large portfolios',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Enterprise',
                'name_bn' => 'এন্টারপ্রাইজ',
                'credits' => 5000,
                'price' => 12999,
                'bonus_credits' => 1250,
                'description' => 'For large organizations with high volume needs',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        $this->command->info('Credit packages seeded successfully!');
    }
}
