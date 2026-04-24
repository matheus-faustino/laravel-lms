<?php

namespace App\Interfaces\Services;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CourseServiceInterface
{
    /**
     * Retrieve all courses from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Course>
     */
    public function getAllCourses(array $columns = ['*']): Collection;

    /**
     * Retrieve a single course by ID.
     *
     * @param int   $id      The course ID.
     * @param array $colums  The columns to retrieve.
     * @return ?Course
     */
    public function getCourse(int $id, array $colums = ['*']): ?Course;

    /**
     * Create a new course with the given attributes.
     *
     * @param array $attributes The course attributes.
     * @return Course
     */
    public function createCourse(array $attributes = []): Course;

    /**
     * Update an existing course by ID.
     *
     * @param int   $id         The course ID.
     * @param array $attributes The attributes to update.
     * @return Course
     */
    public function updateCourse(int $id, array $attributes = []): Course;

    /**
     * Delete a course by ID.
     *
     * @param int $id The course ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteCourse(int $id): bool;

    /**
     * Get paginated courses based on given criteria
     * 
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function getPaginatedCourses(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator;
}
