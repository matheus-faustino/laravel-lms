<?php

namespace App\Services;

use App\Interfaces\Services\ModuleServiceInterface;
use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ModuleService implements ModuleServiceInterface
{
    /** @inheritDoc */
    public function getAllModules(array $columns = ['*']): Collection
    {
        return Module::query()->get($columns);
    }

    /** @inheritDoc */
    public function getModule(int $id, array $colums = ['*']): ?Module
    {
        return Module::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createModule(array $attributes = []): Module
    {
        return DB::transaction(fn(): Module => Module::query()->create($attributes));
    }

    /** @inheritDoc */
    public function updateModule(int $id, array $attributes = []): Module
    {
        return DB::transaction(function () use ($id, $attributes): Module {
            $module = Module::query()->findOrFail($id);

            $module->update($attributes);

            return $module->fresh();
        });
    }

    /** @inheritDoc */
    public function deleteModule(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $module = Module::query()->findOrFail($id);

            return $module->delete();
        });
    }

    /** @inheritDoc */
    public function getPaginatedModules(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator
    {
        return Module::query()->where($criteria)->paginate($perPage, $colums);
    }

    /** @inheritDoc */
    public function updateOrder(array $modules): bool
    {
        foreach ($modules as $module) {
            $this->updateModule($module['id'], ['order' => $module['order']]);
        }

        return true;
    }
}
