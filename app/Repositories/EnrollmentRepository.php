<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function getByStudent(int $studentId): Collection
    {
        return $this->model
            ->where('student_id', $studentId)
            ->with(['course'])
            ->orderBy('enrolled_at', 'desc')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByCourse(int $courseId): Collection
    {
        return $this->model
            ->where('course_id', $courseId)
            ->with(['student'])
            ->orderBy('enrolled_at', 'desc')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function isEnrolled(int $studentId, int $courseId): bool
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('active', true)
            ->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function searchEnrollments(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->with(['student', 'course']);

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->where('active', true)->whereNull('completed_at');
                    break;
                case 'completed':
                    $query->whereNotNull('completed_at');
                    break;
                case 'cancelled':
                    $query->where('active', false);
                    break;
            }
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('enrolled_at', 'desc')->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveEnrollments(int $studentId): Collection
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('active', true)
            ->with(['course'])
            ->orderBy('enrolled_at', 'desc')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCompletedEnrollments(int $studentId): Collection
    {
        return $this->model
            ->where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->with(['course'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }
}
