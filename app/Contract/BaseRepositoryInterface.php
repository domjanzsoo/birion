<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function getAll(): Collection;
    public function getAllPaginated(int $pagination = 10): LengthAwarePaginator;
    public function getById(int $id): Model;
    public function create(array $attributes): Model;
    public function update(Model $user, array $attributes): Model;
    public function delete(Model | int $user): void;
}
