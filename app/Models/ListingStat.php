<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'view_count',
        'contact_count',
        'favorite_count',
        'inquiry_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'contact_count' => 'integer',
        'favorite_count' => 'integer',
        'inquiry_count' => 'integer',
        'last_viewed_at' => 'datetime',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
