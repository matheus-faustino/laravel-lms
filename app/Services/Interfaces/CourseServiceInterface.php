<?php

namespace App\Services\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourseServiceInterface extends BaseServiceInterface
{
    /**
     * Get active courses only
     *
     * @return Collection
     */
    public function getActiveCourses(): Collection;

    /**
     * Search courses with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchCourses(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get course with additional statistics
     *
     * @param int $id
     * @return Course|null
     */
    public function findWithStats(int $id): ?Course;

    /**
     * Get courses available for student
     *
     * @param int $studentId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCoursesForStudent(int $studentId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get course details for student view
     *
     * @param int $courseId
     * @param int $studentId
     * @return Course|null
     */
    public function getCourseForStudent(int $courseId, int $studentId): ?Course;

    /**
     * Toggle course active status
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus(int $id): bool;
}
