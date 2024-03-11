<?php

namespace App\Repositories;

use App\Contract\BaseRepositoryInterface;
use App\Models\User;
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

    public function getAll(): Collection
    {
        return $this->model::all();
    }

    public function getById(int $id): Model
    {
        return Model::find($id);
    }

    public function create(array $attributes): Model
    {
        return User::create($attributes);
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
                $model::destroy($model);
                break;
            case 'object':
                $model->delete();
                break;
            default:
                throw new Exception('Invalid model type received');
        }
    }
}