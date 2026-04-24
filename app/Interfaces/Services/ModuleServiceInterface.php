<?php

namespace App\Interfaces\Services;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ModuleServiceInterface
{
    /**
     * Retrieve all modules from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Module>
     */
    public function getAllModules(array $columns = ['*']): Collection;

    /**
     * Retrieve a single module by ID.
     *
     * @param int   $id      The module ID.
     * @param array $colums  The columns to retrieve.
     * @return ?Module
     */
    public function getModule(int $id, array $colums = ['*']): ?Module;

    /**
     * Create a new module with the given attributes.
     *
     * @param array $attributes The module attributes.
     * @return Module
     */
    public function createModule(array $attributes = []): Module;

    /**
     * Update an existing module by ID.
     *
     * @param int   $id         The module ID.
     * @param array $attributes The attributes to update.
     * @return Module
     */
    public function updateModule(int $id, array $attributes = []): Module;

    /**
     * Delete a module by ID.
     *
     * @param int $id The module ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteModule(int $id): bool;

    /**
     * Get paginated modules based on given criteria
     * 
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function getPaginatedModules(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator;

    /**
     * Update modules order.
     *
     * @param array $attributes The attributes to update.
     * @return bool
     */
    public function updateOrder(array $attributes): bool;
}
