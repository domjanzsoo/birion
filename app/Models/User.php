<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'verified',
        'user_permissions_list',
        'all_user_permissions_count',
        'all_user_roles_count',
        'user_roles_list'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getVerifiedAttribute(): string
    {
        $now = new DateTime();
        
        return isset($this->email_verified_at) && $this->email_verified_at->getTimestamp() < $now->getTimestamp() ? 'verified' : 'unverified';
    }

    public function getAllUserPermissions(): array
    {
        $userPermissionsFromRoles = [];
        $userPermissions = $this->permissions()->pluck('permissions.name')->toArray();
        
        $this->roles()->each(function($role) use (&$userPermissionsFromRoles) {
            $userPermissionsFromRoles = array_unique(array_merge($userPermissionsFromRoles, $role->permissions()->pluck('permissions.name')->toArray()), SORT_REGULAR);
        });

        return array_unique(array_merge($userPermissions, $userPermissionsFromRoles), SORT_REGULAR);
    }

    public function getAllUserPermissionsCountAttribute(): int
    {
        return count($this->getAllUserPermissions());
    }

    public function getUserPermissionsListAttribute(): string
    {
        return implode(', ', $this->getAllUserPermissions());
    }

    public function getAllUserRolesCountAttribute(): int
    {
        return $this->roles->count();
    }

    public function getUserRolesListAttribute(): string
    {
        $roles = $this->roles()->pluck('roles.name')->toArray();

        return implode(', ', $roles);
    }
}
