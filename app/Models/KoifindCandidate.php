<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoifindCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'koifind_request_id',
        'listing_id',
        'ai_score',
        'ai_reasons',
        'ai_features_match',
        'ai_location_match',
        'ai_price_match',
        'user_feedback',
        'shortlisted',
        'viewed_at',
        'contacted_at',
        'notes',
    ];

    protected $casts = [
        'ai_score' => 'decimal:2',
        'ai_reasons' => 'array',
        'ai_features_match' => 'decimal:2',
        'ai_location_match' => 'decimal:2',
        'ai_price_match' => 'decimal:2',
        'shortlisted' => 'boolean',
        'viewed_at' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    // Relationships
    public function koifindRequest()
    {
        return $this->belongsTo(KoifindRequest::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Scopes
    public function scopeShortlisted($query)
    {
        return $query->where('shortlisted', true);
    }

    public function scopeHighScored($query, $minScore = 70)
    {
        return $query->where('ai_score', '>=', $minScore);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ai_score', 'desc');
    }
}
