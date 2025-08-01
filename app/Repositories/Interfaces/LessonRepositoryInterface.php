<?php

namespace App\Repositories\Interfaces;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

interface LessonRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get lessons by module ID
     *
     * @param int $moduleId
     * @return Collection
     */
    public function getByModule(int $moduleId): Collection;

    /**
     * Update lesson order
     *
     * @param int $id
     * @param int $order
     * @return bool
     */
    public function updateOrder(int $id, int $order): bool;

    /**
     * Get next order number for module
     *
     * @param int $moduleId
     * @return int
     */
    public function getNextOrder(int $moduleId): int;

    /**
     * Reorder lessons after deletion
     *
     * @param int $moduleId
     * @param int $deletedOrder
     * @return void
     */
    public function reorderAfterDeletion(int $moduleId, int $deletedOrder): void;

    /**
     * Get lesson with progress for student
     *
     * @param int $lessonId
     * @param int $studentId
     * @return Lesson|null
     */
    public function findWithProgress(int $lessonId, int $studentId): ?Lesson;
}
