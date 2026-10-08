<?php

use App\Models\SiteSetting;

if (!function_exists('setting')) {
    /**
     * Get a site setting value by key with optional default.
     * Usage: setting('site_name', 'ToLetKoi')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $cache = null;

        if ($cache === null) {
            $cache = SiteSetting::pluck('value', 'key')->toArray();
        }

        return $cache[$key] ?? $default;
    }
}
