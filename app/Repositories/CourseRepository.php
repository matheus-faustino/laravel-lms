<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function getByStatus(bool $active): Collection
    {
        return $this->model->where('active', $active)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function searchCourses(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveCourses(): Collection
    {
        return $this->getByStatus(true);
    }

    /**
     * {@inheritDoc}
     */
    public function findWithCounts(int $id): ?Course
    {
        return $this->model
            ->withCount(['modules', 'enrollments'])
            ->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getCoursesForStudent(int $studentId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->where('courses.active', true)
            ->leftJoin('enrollments', function ($join) use ($studentId) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.student_id', $studentId)
                    ->where('enrollments.active', true);
            })
            ->select(
                'courses.*',
                'enrollments.id as enrollment_id',
                'enrollments.progress_percentage',
                'enrollments.enrolled_at',
                'enrollments.completed_at'
            );

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('courses.title', 'LIKE', "%{$search}%")
                    ->orWhere('courses.description', 'LIKE', "%{$search}%");
            });
        }

        if (isset($filters['enrolled'])) {
            if ($filters['enrolled']) {
                $query->whereNotNull('enrollments.id');
            } else {
                $query->whereNull('enrollments.id');
            }
        }

        return $query->orderBy('courses.created_at', 'desc')->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function findForStudent(int $courseId, int $studentId): ?Course
    {
        return $this->model
            ->with(['modules.lessons'])
            ->leftJoin('enrollments', function ($join) use ($studentId) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.student_id', $studentId)
                    ->where('enrollments.active', true);
            })
            ->select(
                'courses.*',
                'enrollments.id as enrollment_id',
                'enrollments.progress_percentage',
                'enrollments.enrolled_at',
                'enrollments.completed_at'
            )
            ->where('courses.id', $courseId)
            ->where('courses.active', true)
            ->first();
    }
}
