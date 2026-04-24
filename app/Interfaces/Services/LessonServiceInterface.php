<?php

namespace App\Interfaces\Services;

use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LessonServiceInterface
{
    /**
     * Retrieve all lessons from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, Lesson>
     */
    public function getAllLessons(array $columns = ['*']): Collection;

    /**
     * Retrieve a single lesson by ID.
     *
     * @param int   $id      The lesson ID.
     * @param array $colums  The columns to retrieve.
     * @return ?Lesson
     */
    public function getLesson(int $id, array $colums = ['*']): ?Lesson;

    /**
     * Create a new lesson with the given attributes.
     *
     * @param array $attributes The lesson attributes.
     * @return Lesson
     */
    public function createLesson(array $attributes = []): Lesson;

    /**
     * Update an existing lesson by ID.
     *
     * @param int   $id         The lesson ID.
     * @param array $attributes The attributes to update.
     * @return Lesson
     */
    public function updateLesson(int $id, array $attributes = []): Lesson;

    /**
     * Delete a lesson by ID.
     *
     * @param int $id The lesson ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteLesson(int $id): bool;

    /**
     * Update lessons order.
     *
     * @param array $attributes The attributes to update.
     * @return bool
     */
    public function updateOrder(array $attributes): bool;
}
