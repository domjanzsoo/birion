<?php

namespace App\Contract;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function createUser(array $fields, array $permissions = [], array $roles = []): User;
}
