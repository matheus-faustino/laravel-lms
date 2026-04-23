<?php

namespace App\Interfaces\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{

    /**
     * Retrieve all categories from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Category>
     */
    public function getAllCategories(array $columns = ['*']): Collection;

    /**
     * Retrieve all categories that doesn't have a parent category.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Category>
     */
    public function getAllParentCategories(array $columns = ['*']): Collection;

    /**
     * Retrieve a single category by ID.
     *
     * @param int   $id      The category ID.
     * @param array $colums  The columns to retrieve.
     * @return ?Category
     */
    public function getCategory(int $id, array $colums = ['*']): ?Category;

    /**
     * Create a new category with the given attributes.
     *
     * @param array $attributes The category attributes.
     * @return Category
     */
    public function createCategory(array $attributes = []): Category;

    /**
     * Update an existing category by ID.
     *
     * @param int   $id         The category ID.
     * @param array $attributes The attributes to update.
     * @return Category
     */
    public function updateCategory(int $id, array $attributes = []): Category;

    /**
     * Delete a category by ID.
     *
     * @param int $id The category ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteCategory(int $id): bool;

    /**
     * Get paginated categories based on given criteria
     * 
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function getPaginatedCategories(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator;
}
