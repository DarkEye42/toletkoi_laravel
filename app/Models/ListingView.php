<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingView extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
