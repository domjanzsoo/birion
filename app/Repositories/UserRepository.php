<?php

namespace App\Repositories;

use App\Contract\UserRepositoryInterface as ContractUserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements ContractUserRepositoryInterface
{
    public function getAll(): Collection
    {
        return User::all();
    }

    public function getById(int $id): User
    {
        return User::find($id);
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }
}