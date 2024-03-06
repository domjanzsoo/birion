<?php

namespace App\Contract;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): User;
    public function create(array $attributes): User;
    public function update(User $user, array $attributes): User;
}
