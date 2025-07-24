<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get courses by status
     *
     * @param bool $active
     * @return Collection
     */
    public function getByStatus(bool $active): Collection;

    /**
     * Search courses with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchCourses(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active courses
     *
     * @return Collection
     */
    public function getActiveCourses(): Collection;

    /**
     * Get course with modules and lessons count
     *
     * @param int $id
     * @return Course|null
     */
    public function findWithCounts(int $id): ?Course;

    /**
     * Get courses with enrollment status for student
     *
     * @param int $studentId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCoursesForStudent(int $studentId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get course with modules for student view
     *
     * @param int $courseId
     * @param int $studentId
     * @return Course|null
     */
    public function findForStudent(int $courseId, int $studentId): ?Course;
}
