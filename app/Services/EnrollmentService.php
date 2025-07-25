<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\EnrollmentServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EnrollmentService extends BaseService implements EnrollmentServiceInterface
{
    public function __construct(
        EnrollmentRepositoryInterface $repository,
        private CourseRepositoryInterface $courseRepository,
        private UserRepositoryInterface $userRepository
    ) {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function enrollStudent(int $studentId, int $courseId): Enrollment
    {
        if (!$this->canEnroll($studentId, $courseId)) {
            throw new \Exception('Student cannot be enrolled in this course');
        }

        $data = [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'progress_percentage' => 0.00,
            'active' => true,
        ];

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function cancelEnrollment(int $enrollmentId): bool
    {
        return $this->repository->update($enrollmentId, [
            'active' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getStudentEnrollments(int $studentId): Collection
    {
        return $this->repository->getByStudent($studentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCourseEnrollments(int $courseId): Collection
    {
        return $this->repository->getByCourse($courseId);
    }

    /**
     * {@inheritDoc}
     */
    public function canEnroll(int $studentId, int $courseId): bool
    {
        $student = $this->userRepository->find($studentId);
        $course = $this->courseRepository->find($courseId);

        if (!$student || !$course) {
            return false;
        }

        if (!$student->isStudent() || !$course->active) {
            return false;
        }

        return !$this->repository->isEnrolled($studentId, $courseId);
    }

    /**
     * {@inheritDoc}
     */
    public function searchEnrollments(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchEnrollments($filters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment
    {
        return $this->repository->findByStudentAndCourse($studentId, $courseId);
    }

    /**
     * {@inheritDoc}
     */
    public function getEnrollmentStats(): array
    {
        $allEnrollments = $this->repository->all();

        $totalEnrollments = $allEnrollments->count();
        $activeEnrollments = $allEnrollments->where('active', true)->count();
        $completedEnrollments = $allEnrollments->whereNotNull('completed_at')->count();
        $cancelledEnrollments = $allEnrollments->where('active', false)->count();

        return [
            'total' => $totalEnrollments,
            'active' => $activeEnrollments,
            'completed' => $completedEnrollments,
            'cancelled' => $cancelledEnrollments,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function processSearchFilters(array $filters): array
    {
        $processed = [];

        if (isset($filters['course_id'])) {
            $processed['course_id'] = $filters['course_id'];
        }

        if (isset($filters['student_id'])) {
            $processed['student_id'] = $filters['student_id'];
        }

        if (isset($filters['status'])) {
            $processed['status'] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $processed['search'] = $filters['search'];
        }

        return $processed;
    }
}
