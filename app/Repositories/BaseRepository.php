<?php

namespace App\Repositories;

use App\Contract\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

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

    public function getAllPaginated(int $pagination = 10, array $with = [], array $search = [], array $filters = []): LengthAwarePaginator
    {
        $results = $this->model::with($with);

        if (isset($search['value'])) {
            foreach ($search['searchFields'] as $field) {
                $fieldAssociation = explode('.', $field);

                if (count($fieldAssociation) > 1) {
                    $results->orWhereHas($fieldAssociation[0], function (Builder $q) use ($search, $fieldAssociation) {
                        $q->where($fieldAssociation[1], 'LIKE', '%'. $search['value'] . '%');
                    });
                } else {
                    $results->orWhere($fieldAssociation[0], 'LIKE', '%' . $search['value'] . '%');
                }
            }
        }

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $fieldAssociation = explode('.', $filter['property']);

                if (!empty($filter['value'])) {
                    if (count($fieldAssociation) > 1) {
                        $results->orWhereHas($fieldAssociation[0], function (Builder $q) use($filter, $fieldAssociation) {
                            if ($filter['condition'] === 'between') {
                                $range = explode('-', $filter['value']);

                                $q->whereBetween($fieldAssociation[1], [$range[0], $range[1]]);
                            } else {
                                $q->where($fieldAssociation[1], $filter['condition'], $filter['value']);
                            }
                        });
                    } else {
                        if ($filter['condition'] === 'between') {
                            $range = explode('-', $filter['value']);

                            $results->whereBetween($filter['property'], [$range[0], $range[1]]);
                        } else {
                            $results->where($filter['property'], $filter['condition'], $filter['value']);
                        }
                    }
                }
            }
        }

        return $results->paginate($pagination);
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