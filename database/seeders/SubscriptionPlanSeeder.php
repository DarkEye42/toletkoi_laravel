<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'name_bn' => 'ফ্রি',
                'slug' => 'free',
                'description' => 'Basic listing features for individual landlords',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_listings' => 2,
                'max_images_per_listing' => 3,
                'is_featured_included' => false,
                'is_promoted_included' => false,
                'is_analytics_included' => false,
                'is_priority_support' => false,
                'features' => [
                    '2 property listings',
                    '3 images per listing',
                    'Basic listing features',
                    'Email notifications',
                ],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'name_bn' => 'বেসিক',
                'slug' => 'basic',
                'description' => 'More listings and images for growing landlords',
                'price_monthly' => 499,
                'price_yearly' => 4999,
                'max_listings' => 10,
                'max_images_per_listing' => 5,
                'is_featured_included' => false,
                'is_promoted_included' => false,
                'is_analytics_included' => true,
                'is_priority_support' => false,
                'features' => [
                    '10 property listings',
                    '5 images per listing',
                    'Basic listing features',
                    'Email & SMS notifications',
                    'Listing analytics',
                ],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional',
                'name_bn' => 'প্রফেশনাল',
                'slug' => 'professional',
                'description' => 'Featured listings and promotion for maximum visibility',
                'price_monthly' => 1499,
                'price_yearly' => 14999,
                'max_listings' => 50,
                'max_images_per_listing' => 10,
                'is_featured_included' => true,
                'is_promoted_included' => true,
                'is_analytics_included' => true,
                'is_priority_support' => true,
                'features' => [
                    '50 property listings',
                    '10 images per listing',
                    'Featured listing badge',
                    'Listing promotion',
                    'Advanced analytics',
                    'Priority support',
                    'All notification types',
                ],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'name_bn' => 'বিজনেস',
                'slug' => 'business',
                'description' => 'Unlimited listings for agencies and large portfolios',
                'price_monthly' => 4999,
                'price_yearly' => 49999,
                'max_listings' => -1, // Unlimited
                'max_images_per_listing' => 20,
                'is_featured_included' => true,
                'is_promoted_included' => true,
                'is_analytics_included' => true,
                'is_priority_support' => true,
                'features' => [
                    'Unlimited property listings',
                    '20 images per listing',
                    'Always featured',
                    'Auto-promotion',
                    'Advanced analytics & reports',
                    'Dedicated account manager',
                    'All notification types',
                    'API access',
                ],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Enterprise',
                'name_bn' => 'এন্টারপ্রাইজ',
                'slug' => 'enterprise',
                'description' => 'Custom solutions for large organizations',
                'price_monthly' => 14999,
                'price_yearly' => 149999,
                'max_listings' => -1,
                'max_images_per_listing' => 50,
                'is_featured_included' => true,
                'is_promoted_included' => true,
                'is_analytics_included' => true,
                'is_priority_support' => true,
                'features' => [
                    'Unlimited everything',
                    '50 images per listing',
                    'Custom branding',
                    'White-label options',
                    'Full API access',
                    'Bulk operations',
                    'Custom integrations',
                    'Dedicated support team',
                ],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
