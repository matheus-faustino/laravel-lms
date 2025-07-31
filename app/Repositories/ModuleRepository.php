<?php

namespace App\Repositories;

use App\Models\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    public function __construct(Module $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function getByCourse(int $courseId): Collection
    {
        return $this->model
            ->where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findWithLessons(int $id): ?Module
    {
        return $this->model
            ->with(['lessons' => function ($query) {
                $query->orderBy('order');
            }])
            ->find($id);
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
    public function getNextOrder(int $courseId): int
    {
        $maxOrder = $this->model
            ->where('course_id', $courseId)
            ->max('order');

        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * {@inheritDoc}
     */
    public function reorderAfterDeletion(int $courseId, int $deletedOrder): void
    {
        $this->model
            ->where('course_id', $courseId)
            ->where('order', '>', $deletedOrder)
            ->decrement('order');
    }
}
