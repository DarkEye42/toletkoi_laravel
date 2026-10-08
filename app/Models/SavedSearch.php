<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'criteria',
        'notify_new_matches',
        'match_count',
        'last_checked_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'notify_new_matches' => 'boolean',
        'match_count' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
