<?php

namespace App\Services\Interfaces;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

interface LessonServiceInterface extends BaseServiceInterface
{
    /**
     * Get lessons by module ID
     *
     * @param int $moduleId
     * @return Collection
     */
    public function getLessonsByModule(int $moduleId): Collection;

    /**
     * Create lesson with automatic ordering
     *
     * @param array $data
     * @return Lesson
     */
    public function createLesson(array $data): Lesson;

    /**
     * Update lesson order
     *
     * @param int $id
     * @param int $order
     * @return bool
     */
    public function updateOrder(int $id, int $order): bool;

    /**
     * Delete lesson and reorder others
     *
     * @param int $id
     * @return bool
     */
    public function deleteLesson(int $id): bool;

    /**
     * Get lesson with progress for student
     *
     * @param int $lessonId
     * @param int $studentId
     * @return Lesson|null
     */
    public function findWithProgress(int $lessonId, int $studentId): ?Lesson;

    /**
     * Validate lesson content based on type
     *
     * @param string $type
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function validateLessonContent(string $type, array $data): array;
}
