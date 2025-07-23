<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * Find a model by its primary key
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Find a model by its primary key or throw an exception
     *
     * @param int $id
     * @param array $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Get all models
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Create a new model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a model
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a model
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find models by specific criteria
     *
     * @param array $criteria
     * @param array $columns
     * @return Collection
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection;

    /**
     * Find a single model by specific criteria
     *
     * @param array $criteria
     * @param array $columns
     * @return Model|null
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model;

    /**
     * Get paginated results
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Search models by criteria with pagination
     *
     * @param array $criteria
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function search(array $criteria, int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Count total models
     *
     * @return int
     */
    public function count(): int;

    /**
     * Check if model exists by criteria
     *
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria): bool;
}
