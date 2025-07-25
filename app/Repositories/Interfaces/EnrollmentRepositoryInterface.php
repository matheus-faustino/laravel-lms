<?php

namespace App\Repositories\Interfaces;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EnrollmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get enrollments by student ID
     *
     * @param int $studentId
     * @return Collection
     */
    public function getByStudent(int $studentId): Collection;

    /**
     * Get enrollments by course ID
     *
     * @param int $courseId
     * @return Collection
     */
    public function getByCourse(int $courseId): Collection;

    /**
     * Find enrollment by student and course
     *
     * @param int $studentId
     * @param int $courseId
     * @return Enrollment|null
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment;

    /**
     * Check if student is enrolled in course
     *
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function isEnrolled(int $studentId, int $courseId): bool;

    /**
     * Search enrollments with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchEnrollments(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active enrollments for student
     *
     * @param int $studentId
     * @return Collection
     */
    public function getActiveEnrollments(int $studentId): Collection;

    /**
     * Get completed enrollments for student
     *
     * @param int $studentId
     * @return Collection
     */
    public function getCompletedEnrollments(int $studentId): Collection;
}
