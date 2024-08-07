<?php

namespace App\Repositories;

use App\Contract\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(string $orderBy = null): Collection
    {
        if (isset($orderBy)) {
            return $this->model::query()->orderBy('name')->get();
        }

        return $this->model::all();
    }

    public function getAllPaginated(int $pagination = 10, array $with = null): LengthAwarePaginator
    {
        return isset($with) ? $this->model::with(['images'])->paginate($pagination) : $this->model::paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model::create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model;
    }

    public function delete(Model | array | int $model): void
    {
        switch (gettype($model)) {
            case 'integer':
            case 'array':
                $this->model::destroy($model);
                break;
            case 'object':
                $this->model->delete();
                break;
            default:
                throw new Exception('Invalid model type received');
        }
    }
}