<?php

namespace App\Interfaces\Services;

use App\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EnrollmentServiceInterface
{
    /**
     * Retrieve all enrollments from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Enrollment>
     */
    public function getAllEnrollments(array $columns = ['*']): Collection;

    /**
     * Retrieve a single enrollment by ID.
     *
     * @param int   $id      The enrollment ID.
     * @param array $colums  The columns to retrieve.
     * @return ?Enrollment
     */
    public function getEnrollment(int $id, array $colums = ['*']): ?Enrollment;

    /**
     * Create a new enrollment with the given attributes.
     *
     * @param array $attributes The enrollment attributes.
     * @return Enrollment
     */
    public function createEnrollment(array $attributes = []): Enrollment;

    /**
     * Update an existing enrollment by ID.
     *
     * @param int   $id         The enrollment ID.
     * @param array $attributes The attributes to update.
     * @return Enrollment
     */
    public function updateEnrollment(int $id, array $attributes = []): Enrollment;

    /**
     * Delete a enrollment by ID.
     *
     * @param int $id The enrollment ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteEnrollment(int $id): bool;

    /**
     * Retrieve the active enrollment for a given user and course.
     *
     * @param int $userId   The user ID.
     * @param int $courseId The course ID.
     * @return ?Enrollment
     */
    public function getActiveEnrollment(int $userId, int $courseId): ?Enrollment;

    /**
     * Get paginated enrollments based on given criteria
     * 
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function getPaginatedEnrollments(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator;
}
