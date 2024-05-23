<?php

namespace App\Repositories;

use App\Contract\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function createRole(string $name, array $permissions = []): Role
    {
        $role = parent::create(['name' => $name]);

        if (count($permissions) > 0) {
            $role->permissions()->attach($permissions);
        }

        $role->save();

        return $role;
    }
}