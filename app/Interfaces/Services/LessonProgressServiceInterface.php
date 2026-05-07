<?php

namespace App\Interfaces\Services;

use App\Models\LessonProgress;

interface LessonProgressServiceInterface
{
    /**
     * Mark a lesson as watched for a given user.
     *
     * @param int $userId   The user ID.
     * @param int $lessonId The lesson ID.
     * @return LessonProgress
     */
    public function markAsWatched(int $userId, int $lessonId): LessonProgress;

    /**
     * Check if a lesson has been watched by a given user.
     *
     * @param int $userId   The user ID.
     * @param int $lessonId The lesson ID.
     * @return bool
     */
    public function isLessonWatched(int $userId, int $lessonId): bool;

    /**
     * Get the IDs of lessons watched by a user within a given course.
     *
     * @param int $userId   The user ID.
     * @param int $courseId The course ID.
     * @return array<int>
     */
    public function getWatchedLessonIds(int $userId, int $courseId): array;

    /**
     * Get progress statistics for a user on a given course.
     *
     * @param int $userId   The user ID.
     * @param int $courseId The course ID.
     * @return array{total: int, watched: int, percentage: float}
     */
    public function getCourseProgress(int $userId, int $courseId): array;
}
