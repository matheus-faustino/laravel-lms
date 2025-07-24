<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseService extends BaseService implements CourseServiceInterface
{
    public function __construct(CourseRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveCourses(): Collection
    {
        return $this->repository->getActiveCourses();
    }

    /**
     * {@inheritDoc}
     */
    public function searchCourses(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchCourses($filters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function findWithStats(int $id): ?Course
    {
        return $this->repository->findWithCounts($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getCoursesForStudent(int $studentId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getCoursesForStudent($studentId, $filters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getCourseForStudent(int $courseId, int $studentId): ?Course
    {
        return $this->repository->findForStudent($courseId, $studentId);
    }

    /**
     * {@inheritDoc}
     */
    public function toggleStatus(int $id): bool
    {
        $course = $this->repository->findOrFail($id);

        return $this->repository->update($id, [
            'active' => !$course->active
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function processSearchFilters(array $filters): array
    {
        $processed = [];

        if (isset($filters['search'])) {
            $processed['search'] = $filters['search'];
        }

        if (isset($filters['active'])) {
            $processed['active'] = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN);
        }

        return $processed;
    }
}
