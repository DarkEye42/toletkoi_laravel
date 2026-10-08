<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'nid_number',
        'avatar',
        'bio',
        'address',
        'address_bn',
        'lat',
        'lng',
        'division_id',
        'district_id',
        'area_id',
        'company_name',
        'website',
        'social_facebook',
        'social_instagram',
        'social_linkedin',
        'phone_verified_at',
        'nid_verified_at',
        'photo_verified_at',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'phone_verified_at' => 'datetime',
        'nid_verified_at' => 'datetime',
        'photo_verified_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
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

    // Helpers
    public function getAvatarUrl()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
}
