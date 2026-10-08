<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUnlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'credits_used',
        'contact_type',
        'ip_address',
    ];

    protected $casts = [
        'credits_used' => 'decimal:2',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Listing::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
