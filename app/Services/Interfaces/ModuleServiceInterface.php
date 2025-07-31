<?php

namespace App\Services\Interfaces;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleServiceInterface extends BaseServiceInterface
{
    /**
     * Get modules by course ID
     *
     * @param int $courseId
     * @return Collection
     */
    public function getModulesByCourse(int $courseId): Collection;

    /**
     * Create module with automatic ordering
     *
     * @param array $data
     * @return Module
     */
    public function createModule(array $data): Module;

    /**
     * Get module with lessons
     *
     * @param int $id
     * @return Module|null
     */
    public function findWithLessons(int $id): ?Module;

    /**
     * Update module order
     *
     * @param int $id
     * @param int $order
     * @return bool
     */
    public function updateOrder(int $id, int $order): bool;

    /**
     * Delete module and reorder others
     *
     * @param int $id
     * @return bool
     */
    public function deleteModule(int $id): bool;
}
