<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The model instance
     *
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->first($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function search(array $criteria, int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (str_contains($field, '.like')) {
                $field = str_replace('.like', '', $field);
                $query->where($field, 'LIKE', "%{$value}%");
            } else {
                $query->where($field, $value);
            }
        }

        return $query->paginate($perPage, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * {@inheritDoc}
     */
    public function exists(array $criteria): bool
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->exists();
    }
}
