<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'ToLetKoi', 'type' => 'text', 'description' => 'Website name'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Find Your Perfect Rental', 'type' => 'text', 'description' => 'Website tagline'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Bangladesh\'s leading rental marketplace', 'type' => 'textarea', 'description' => 'Website description'],
            ['group' => 'general', 'key' => 'site_logo', 'value' => 'images/logo.png', 'type' => 'image', 'description' => 'Website logo'],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => 'images/favicon.ico', 'type' => 'image', 'description' => 'Website favicon'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'support@toletkoi.com', 'type' => 'email', 'description' => 'Contact email'],
            ['group' => 'general', 'key' => 'contact_phone', 'value' => '+8801700000000', 'type' => 'text', 'description' => 'Contact phone'],
            ['group' => 'general', 'key' => 'contact_address', 'value' => 'Dhaka, Bangladesh', 'type' => 'text', 'description' => 'Contact address'],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'ToLetKoi - Find Your Perfect Rental in Bangladesh', 'type' => 'text', 'description' => 'Default meta title'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Bangladesh\'s leading rental marketplace. Find apartments, houses, rooms, offices, shops, and more.', 'type' => 'textarea', 'description' => 'Default meta description'],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => 'rent,bangladesh,apartment,house,room,office,property', 'type' => 'textarea', 'description' => 'Default meta keywords'],

            // Social
            ['group' => 'social', 'key' => 'facebook_url', 'value' => 'https://facebook.com/toletkoi', 'type' => 'url', 'description' => 'Facebook page URL'],
            ['group' => 'social', 'key' => 'twitter_url', 'value' => 'https://twitter.com/toletkoi', 'type' => 'url', 'description' => 'Twitter profile URL'],
            ['group' => 'social', 'key' => 'instagram_url', 'value' => 'https://instagram.com/toletkoi', 'type' => 'url', 'description' => 'Instagram profile URL'],
            ['group' => 'social', 'key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/toletkoi', 'type' => 'url', 'description' => 'LinkedIn company URL'],
            ['group' => 'social', 'key' => 'youtube_url', 'value' => '', 'type' => 'url', 'description' => 'YouTube channel URL'],

            // Currency
            ['group' => 'currency', 'key' => 'default_currency', 'value' => 'BDT', 'type' => 'text', 'description' => 'Default currency code'],
            ['group' => 'currency', 'key' => 'currency_symbol', 'value' => '৳', 'type' => 'text', 'description' => 'Currency symbol'],
            ['group' => 'currency', 'key' => 'currency_position', 'value' => 'before', 'type' => 'select', 'description' => 'Currency symbol position'],

            // Listing
            ['group' => 'listing', 'key' => 'default_listing_duration', 'value' => '30', 'type' => 'number', 'description' => 'Default listing duration in days'],
            ['group' => 'listing', 'key' => 'max_images_per_listing', 'value' => '10', 'type' => 'number', 'description' => 'Maximum images per listing'],
            ['group' => 'listing', 'key' => 'listing_approval_required', 'value' => 'true', 'type' => 'boolean', 'description' => 'Require admin approval for listings'],
            ['group' => 'listing', 'key' => 'contact_unlock_credits', 'value' => '5', 'type' => 'number', 'description' => 'Credits needed to unlock contact info'],

            // KoiFind
            ['group' => 'koifind', 'key' => 'koifind_enabled', 'value' => 'true', 'type' => 'boolean', 'description' => 'Enable KoiFind AI matching'],
            ['group' => 'koifind', 'key' => 'koifind_max_candidates', 'value' => '10', 'type' => 'number', 'description' => 'Maximum candidates per KoiFind request'],
            ['group' => 'koifind', 'key' => 'koifind_search_radius_km', 'value' => '5', 'type' => 'number', 'description' => 'Default search radius in km'],

            // Email
            ['group' => 'email', 'key' => 'welcome_email_enabled', 'value' => 'true', 'type' => 'boolean', 'description' => 'Send welcome email on registration'],
            ['group' => 'email', 'key' => 'listing_approved_email', 'value' => 'true', 'type' => 'boolean', 'description' => 'Send email when listing is approved'],
            ['group' => 'email', 'key' => 'new_lead_email', 'value' => 'true', 'type' => 'boolean', 'description' => 'Send email on new lead'],

            // Maintenance
            ['group' => 'maintenance', 'key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'description' => 'Enable maintenance mode'],
            ['group' => 'maintenance', 'key' => 'maintenance_message', 'value' => 'We are currently performing maintenance. Please try again later.', 'type' => 'textarea', 'description' => 'Maintenance mode message'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }

        $this->command->info('Site settings seeded successfully!');
    }
}
