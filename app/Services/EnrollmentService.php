<?php

namespace App\Services;

use App\Enums\EnrollmentStatusEnum;
use App\Interfaces\Services\EnrollmentServiceInterface;
use App\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EnrollmentService implements EnrollmentServiceInterface
{
    /** @inheritDoc */
    public function getAllEnrollments(array $columns = ['*']): Collection
    {
        return Enrollment::query()->get($columns);
    }

    /** @inheritDoc */
    public function getEnrollment(int $id, array $colums = ['*']): ?Enrollment
    {
        return Enrollment::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createEnrollment(array $attributes = []): Enrollment
    {
        return DB::transaction(fn (): Enrollment => Enrollment::query()->create($attributes));
    }

    /** @inheritDoc */
    public function updateEnrollment(int $id, array $attributes = []): Enrollment
    {
        return DB::transaction(function () use ($id, $attributes): Enrollment {
            $enrollment = Enrollment::query()->findOrFail($id);

            $enrollment->update($attributes);

            return $enrollment->fresh();
        });
    }

    /** @inheritDoc */
    public function deleteEnrollment(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $enrollment = Enrollment::query()->findOrFail($id);

            return $enrollment->delete();
        });
    }

    /** @inheritDoc */
    public function getPaginatedEnrollments(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator
    {
        return Enrollment::query()->with(['user:id,name,email', 'course:id,title'])->where($criteria)->paginate($perPage, $colums);
    }

    /** @inheritDoc */
    public function getActiveEnrollment(int $userId, int $courseId): ?Enrollment
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', EnrollmentStatusEnum::ACTIVE)
            ->first();
    }
}
