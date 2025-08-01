<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LessonRepository extends BaseRepository implements LessonRepositoryInterface
{
    public function __construct(Lesson $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function getByModule(int $moduleId): Collection
    {
        return $this->model
            ->where('module_id', $moduleId)
            ->orderBy('order')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrder(int $id, int $order): bool
    {
        return $this->model->where('id', $id)->update(['order' => $order]);
    }

    /**
     * {@inheritDoc}
     */
    public function getNextOrder(int $moduleId): int
    {
        $maxOrder = $this->model
            ->where('module_id', $moduleId)
            ->max('order');

        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * {@inheritDoc}
     */
    public function reorderAfterDeletion(int $moduleId, int $deletedOrder): void
    {
        $this->model
            ->where('module_id', $moduleId)
            ->where('order', '>', $deletedOrder)
            ->decrement('order');
    }

    /**
     * {@inheritDoc}
     */
    public function findWithProgress(int $lessonId, int $studentId): ?Lesson
    {
        return $this->model
            ->with(['progress' => function ($query) use ($studentId) {
                $query->whereHas('enrollment', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                });
            }])
            ->find($lessonId);
    }
}
