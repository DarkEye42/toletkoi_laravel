<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'max_listings',
        'max_images_per_listing',
        'is_featured_included',
        'is_promoted_included',
        'is_analytics_included',
        'is_priority_support',
        'features',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'max_listings' => 'integer',
        'max_images_per_listing' => 'integer',
        'is_featured_included' => 'boolean',
        'is_promoted_included' => 'boolean',
        'is_analytics_included' => 'boolean',
        'is_priority_support' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price_monthly');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
