<?php

namespace App\Contract;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function createUser(array $fields, array $permissions = [], array $roles = []): User;

    public function updatePermissions(User $user, array $permissions): User;

    public function updateRoles(User $user, array $roles): User;
}
