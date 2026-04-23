<?php

namespace App\Services;

use App\Interfaces\Services\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService implements CategoryServiceInterface
{
    /** @inheritDoc */
    public function getAllCategories(array $columns = ['*']): Collection
    {
        return Category::query()->get($columns);
    }

    /** @inheritDoc */
    public function getAllParentCategories(array $columns = ['*']): Collection
    {
        return Category::query()->whereNull('category_id')->get($columns);
    }

    /** @inheritDoc */
    public function getCategory(int $id, array $colums = ['*']): ?Category
    {
        return Category::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createCategory(array $attributes = []): Category
    {
        return DB::transaction(function () use ($attributes): Category {
            return Category::query()->create($attributes);
        });
    }

    /** @inheritDoc */
    public function updateCategory(int $id, array $attributes = []): Category
    {
        return DB::transaction(function () use ($id, $attributes): Category {
            $category = Category::query()->findOrFail($id);

            $category->update($attributes);

            return $category->fresh();
        });
    }

    /** @inheritDoc */
    public function deleteCategory(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            return Category::query()->findOrFail($id)->delete();
        });
    }

    /** @inheritDoc */
    public function getPaginatedCategories(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator
    {
        return Category::query()->where($criteria)->paginate($perPage, $colums);
    }
}
