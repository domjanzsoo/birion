<?php

namespace App\Services;

use App\Models\User;

class AccessControlService
{
    private static $instance = null;
    
    private function hasPermissions(string|array $permissions, User $user): bool
    {
        $userPermissions = $user->permissions->pluck('name')->all();
        $userRoles = $user->roles;

        if (is_array($permissions)) {
            foreach($permissions as $permission) {
                if (in_array($permission, $userPermissions)) {
                    return true;
                }
            }
        } elseif (in_array($permissions, $userPermissions)) {
            return true;
        }

        foreach($userRoles as $role) {
            $rolePermissions = $role->permissions->pluck('name')->all();

            if (is_array($permissions)){
                
                foreach ($permissions as $permission) {
                    if (in_array($permission, $rolePermissions)) {
                        return true;
                    }
                }
            } elseif (in_array ($permissions, $rolePermissions)) {
                return true;
            }
        }

        return false;
    }

    private function hasRole(string $role, User $user): bool
    {
        return in_array($role, $user->roles->pluck('name')->all());
    }

    public function canAccess(string|array $permissions = [], User $user, string $role = null)
    {
        $access = $this->hasPermissions($permissions, $user);

        if (!$access && isset($role)) {
            $access = $this->hasRole($role, $user);
        }

        return $access;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AccessControlService();
        }

        return self::$instance;
    }
}
