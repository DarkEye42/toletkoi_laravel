<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * User's profile (extended information).
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * User's preferences (notification and display settings).
     */
    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Custom user_roles pivot (for primary role tracking).
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Role change audit trail.
     */
    public function roleChangeLogs()
    {
        return $this->hasMany(RoleChangeLog::class);
    }

    /**
     * Property listings owned by this user.
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Favorites by this user.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Saved searches by this user.
     */
    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * Credit wallet for this user.
     */
    public function creditWallet()
    {
        return $this->hasOne(CreditWallet::class);
    }

    /**
     * Credit transactions for this user.
     */
    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Active subscription for this user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    /**
     * Leads sent by this user.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Leads received (as listing owner).
     */
    public function receivedLeads()
    {
        return $this->hasMany(Lead::class, 'owner_id');
    }

    /**
     * KoiFind requests by this user.
     */
    public function koifindRequests()
    {
        return $this->hasMany(KoifindRequest::class);
    }

    /**
     * Rental agreements as tenant.
     */
    public function tenantAgreements()
    {
        return $this->hasMany(RentalAgreement::class, 'tenant_id');
    }

    /**
     * Rental agreements as landlord.
     */
    public function landlordAgreements()
    {
        return $this->hasMany(RentalAgreement::class, 'landlord_id');
    }

    /**
     * Organizations owned by this user.
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Service provider profile.
     */
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
     * Audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Activity logs for this user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    /**
     * Get the user's primary role.
     */
    public function getPrimaryRole()
    {
        $userRole = $this->userRoles()->where('is_primary', true)->first()
            ?? $this->userRoles()->first();

        return $userRole?->role;
    }

    /**
     * Check if user can switch roles.
     */
    public function canSwitchRole()
    {
        $primaryRole = $this->getPrimaryRole();
        return $primaryRole && $primaryRole->is_switchable;
    }

    /**
     * Get user's avatar URL.
     */
    public function getAvatarUrl()
    {
        $profile = $this->profile;
        if ($profile && $profile->avatar) {
            return asset('storage/' . $profile->avatar);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Check if user is verified.
     */
    public function isVerified()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if user is phone verified.
     */
    public function isPhoneVerified()
    {
        $profile = $this->profile;
        return $profile && $profile->phone_verified_at !== null;
    }

    /**
     * Get user's full address.
     */
    public function getFullAddress()
    {
        $profile = $this->profile;
        if (!$profile) {
            return '';
        }
        return collect([
            $profile->address,
            $profile->area->name ?? null,
            $profile->district->name ?? null,
            $profile->division->name ?? null,
        ])->filter()->implode(', ');
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return true; // All users are active by default
    }
}
