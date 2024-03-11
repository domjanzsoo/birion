<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): Model;
    public function create(array $attributes): Model;
    public function update(Model $user, array $attributes): Model;
    public function delete(Model | int $user): void;
}
