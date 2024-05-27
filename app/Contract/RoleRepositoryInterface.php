<?php

namespace App\Contract;

use App\Models\Role;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function createRole(string $name, array $permissions): Role;

    public function updatePermissions(Role $role, array $permissions): Role;
}