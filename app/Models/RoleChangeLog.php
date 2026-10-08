<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'previous_role_id',
        'new_role_id',
        'changed_by',
        'reason',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function previousRole()
    {
        return $this->belongsTo(Role::class, 'previous_role_id');
    }

    public function newRole()
    {
        return $this->belongsTo(Role::class, 'new_role_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Helpers
    public function toArray()
    {
        $data = parent::toArray();
        $data['previous_role'] = $this->previousRole?->name;
        $data['new_role'] = $this->newRole?->name;
        $data['changed_by_name'] = $this->changedBy?->name;
        return $data;
    }
}
