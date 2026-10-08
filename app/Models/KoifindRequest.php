<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoifindRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'listing_type',
        'property_type_id',
        'property_subtype_id',
        'division_id',
        'district_id',
        'area_id',
        'min_budget',
        'max_budget',
        'min_bedrooms',
        'max_bedrooms',
        'required_features',
        'preferred_areas',
        'urgency',
        'status',
        'ai_analysis',
        'ai_confidence_score',
        'ai_recommendations',
        'ai_analysis_at',
        'shortlisted_count',
        'contacted_count',
        'total_matches_found',
        'search_radius_km',
        'location_lat',
        'location_lng',
        'expires_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'agent_notes',
        'agent_id',
    ];

    protected $casts = [
        'min_budget' => 'decimal:2',
        'max_budget' => 'decimal:2',
        'min_bedrooms' => 'integer',
        'max_bedrooms' => 'integer',
        'required_features' => 'array',
        'preferred_areas' => 'array',
        'ai_analysis' => 'array',
        'ai_confidence_score' => 'decimal:2',
        'ai_recommendations' => 'array',
        'ai_analysis_at' => 'datetime',
        'shortlisted_count' => 'integer',
        'contacted_count' => 'integer',
        'total_matches_found' => 'integer',
        'search_radius_km' => 'decimal:2',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertySubtype()
    {
        return $this->belongsTo(PropertySubtype::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function candidates()
    {
        return $this->hasMany(KoifindCandidate::class);
    }

    public function shortlists()
    {
        return $this->hasMany(KoifindShortlist::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeWithAiAnalysis($query)
    {
        return $query->whereNotNull('ai_analysis');
    }

    // Helpers
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasAiAnalysis()
    {
        return !empty($this->ai_analysis);
    }
}
