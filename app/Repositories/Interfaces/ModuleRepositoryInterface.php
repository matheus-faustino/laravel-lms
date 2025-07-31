<?php

namespace App\Repositories\Interfaces;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get modules by course ID
     *
     * @param int $courseId
     * @return Collection
     */
    public function getByCourse(int $courseId): Collection;

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
     * Get next order number for course
     *
     * @param int $courseId
     * @return int
     */
    public function getNextOrder(int $courseId): int;

    /**
     * Reorder modules after deletion
     *
     * @param int $courseId
     * @param int $deletedOrder
     * @return void
     */
    public function reorderAfterDeletion(int $courseId, int $deletedOrder): void;
}