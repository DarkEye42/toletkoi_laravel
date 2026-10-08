<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'description',
        'required_documents',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
