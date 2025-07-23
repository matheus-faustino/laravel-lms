<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    /**
     * Get a model by its ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Get a model by its ID or throw exception
     *
     * @param int $id
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Model;

    /**
     * Get models by specific criteria
     *
     * @param array $criteria
     * @return Collection
     */
    public function findBy(array $criteria): Collection;

    /**
     * Get all models
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Create a new model
     *
     * @param array $data
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(array $data): Model;

    /**
     * Update an existing model
     *
     * @param int $id
     * @param array $data
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a model
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;

    /**
     * Get paginated results
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Search models with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if model exists
     *
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria): bool;

    /**
     * Get total count
     *
     * @return int
     */
    public function count(): int;
}
