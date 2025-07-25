<?php

namespace App\Services\Interfaces;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EnrollmentServiceInterface extends BaseServiceInterface
{
    /**
     * Enroll student in course
     *
     * @param int $studentId
     * @param int $courseId
     * @return Enrollment
     * @throws \Exception
     */
    public function enrollStudent(int $studentId, int $courseId): Enrollment;

    /**
     * Cancel enrollment
     *
     * @param int $enrollmentId
     * @return bool
     */
    public function cancelEnrollment(int $enrollmentId): bool;

    /**
     * Get enrollments for student
     *
     * @param int $studentId
     * @return Collection
     */
    public function getStudentEnrollments(int $studentId): Collection;

    /**
     * Get enrollments for course
     *
     * @param int $courseId
     * @return Collection
     */
    public function getCourseEnrollments(int $courseId): Collection;

    /**
     * Check if student can enroll in course
     *
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function canEnroll(int $studentId, int $courseId): bool;

    /**
     * Search enrollments with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchEnrollments(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get enrollment by student and course
     *
     * @param int $studentId
     * @param int $courseId
     * @return Enrollment|null
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment;

    /**
     * Get enrollment statistics
     *
     * @return array
     */
    public function getEnrollmentStats(): array;
}
