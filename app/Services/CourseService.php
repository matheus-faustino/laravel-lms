<?php

namespace App\Services;

use App\Interfaces\Services\CourseServiceInterface;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\CourseStatusEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseService implements CourseServiceInterface
{
    /** @inheritDoc */
    public function getAllCourses(array $columns = ['*']): Collection
    {
        return Course::query()->get($columns);
    }

    /** @inheritDoc */
    public function getCourse(int $id, array $colums = ['*']): ?Course
    {
        return Course::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createCourse(array $attributes = []): Course
    {
        return DB::transaction(function () use ($attributes): Course {
            $attributes['thumbnail'] = $this->uploadImage($attributes['thumbnail'], 'courses/thumbnails');
            $attributes['banner'] = $this->uploadImage($attributes['banner'], 'courses/banners');

            return Course::query()->create($attributes);
        });
    }

    /** @inheritDoc */
    public function updateCourse(int $id, array $attributes = []): Course
    {
        return DB::transaction(function () use ($id, $attributes): Course {
            $course = Course::query()->findOrFail($id);

            if (isset($attributes['thumbnail'])) {
                Storage::disk('public')->delete($course->thumbnail);
                $attributes['thumbnail'] = $this->uploadImage($attributes['thumbnail'], 'courses/thumbnails');
            }

            if (isset($attributes['banner'])) {
                Storage::disk('public')->delete($course->banner);
                $attributes['banner'] = $this->uploadImage($attributes['banner'], 'courses/banners');
            }

            $course->update($attributes);

            return $course->fresh();
        });
    }

    private function uploadImage(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    /** @inheritDoc */
    public function deleteCourse(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $course = Course::query()->findOrFail($id);

            Storage::disk('public')->delete([$course->thumbnail, $course->banner]);

            return $course->delete();
        });
    }

    /** @inheritDoc */
    public function getPaginatedCourses(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator
    {
        return Course::query()->where($criteria)->paginate($perPage, $colums);
    }
}
