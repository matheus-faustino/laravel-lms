<?php

namespace App\Services;

use App\Models\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleService extends BaseService implements ModuleServiceInterface
{
    public function __construct(ModuleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function getModulesByCourse(int $courseId): Collection
    {
        return $this->repository->getByCourse($courseId);
    }

    /**
     * {@inheritDoc}
     */
    public function createModule(array $data): Module
    {
        if (!isset($data['order'])) {
            $data['order'] = $this->repository->getNextOrder($data['course_id']);
        }

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function findWithLessons(int $id): ?Module
    {
        return $this->repository->findWithLessons($id);
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrder(int $id, int $order): bool
    {
        return $this->repository->updateOrder($id, $order);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteModule(int $id): bool
    {
        $module = $this->repository->findOrFail($id);
        $deleted = $this->repository->delete($id);

        if ($deleted) {
            $this->repository->reorderAfterDeletion($module->course_id, $module->order);
        }

        return $deleted;
    }
}
