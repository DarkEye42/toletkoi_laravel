<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoifindShortlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'koifind_request_id',
        'user_id',
        'candidate_id',
        'listing_id',
        'rank',
        'notes',
        'status',
    ];

    // Relationships
    public function koifindRequest()
    {
        return $this->belongsTo(KoifindRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->belongsTo(KoifindCandidate::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('rank');
    }
}
