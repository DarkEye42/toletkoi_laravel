<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_id',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function verification()
    {
        return $this->belongsTo(Verification::class);
    }

    // Helpers
    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }
}
