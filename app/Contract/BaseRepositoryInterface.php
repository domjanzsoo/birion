<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function getAll(string $orderBy): Collection;
    public function getAllPaginated(int $pagination = 10, array $with = null): LengthAwarePaginator;
    public function getById(int $id): Model;
    public function create(array $attributes): Model;
    public function update(Model $user, array $attributes): Model;
    public function delete(Model | int $user): void;
}
