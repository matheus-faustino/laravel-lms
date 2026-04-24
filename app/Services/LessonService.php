<?php

namespace App\Services;

use App\Interfaces\Services\LessonServiceInterface;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LessonService implements LessonServiceInterface
{
    /** @inheritDoc */
    public function getAllLessons(array $columns = ['*']): Collection
    {
        return Lesson::query()->get($columns);
    }

    /** @inheritDoc */
    public function getLesson(int $id, array $colums = ['*']): ?Lesson
    {
        return Lesson::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createLesson(array $attributes = []): Lesson
    {
        return DB::transaction(fn(): Lesson => Lesson::query()->create($attributes));
    }

    /** @inheritDoc */
    public function updateLesson(int $id, array $attributes = []): Lesson
    {
        return DB::transaction(function () use ($id, $attributes): Lesson {
            $lesson = Lesson::query()->findOrFail($id);

            $lesson->update($attributes);

            return $lesson->fresh();
        });
    }

    /** @inheritDoc */
    public function deleteLesson(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $lesson = Lesson::query()->findOrFail($id);

            return $lesson->delete();
        });
    }

    /** @inheritDoc */
    public function updateOrder(array $attributes): bool
    {
        foreach ($attributes as $lesson) {
            $this->updateLesson($lesson['id'], ['order' => $lesson['order']]);
        }

        return true;
    }
}
