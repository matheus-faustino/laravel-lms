<?php

namespace App\Services;

use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use App\Services\Interfaces\LessonServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class LessonService extends BaseService implements LessonServiceInterface
{
    public function __construct(LessonRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function getLessonsByModule(int $moduleId): Collection
    {
        return $this->repository->getByModule($moduleId);
    }

    /**
     * {@inheritDoc}
     */
    public function createLesson(array $data): Lesson
    {
        $data = $this->validateLessonContent($data['type'], $data);

        if (!isset($data['order'])) {
            $data['order'] = $this->repository->getNextOrder($data['module_id']);
        }

        return $this->repository->create($data);
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
    public function deleteLesson(int $id): bool
    {
        $lesson = $this->repository->findOrFail($id);
        $deleted = $this->repository->delete($id);

        if ($deleted) {
            $this->repository->reorderAfterDeletion($lesson->module_id, $lesson->order);
        }

        return $deleted;
    }

    /**
     * {@inheritDoc}
     */
    public function findWithProgress(int $lessonId, int $studentId): ?Lesson
    {
        return $this->repository->findWithProgress($lessonId, $studentId);
    }

    /**
     * {@inheritDoc}
     */
    public function validateLessonContent(string $type, array $data): array
    {
        if ($type === Lesson::TYPE_VIDEO) {
            if (empty($data['video_url'])) {
                throw new \Exception('Video URL is required for video lessons');
            }
            $data['content'] = null;
        } elseif ($type === Lesson::TYPE_TEXT) {
            if (empty($data['content'])) {
                throw new \Exception('Content is required for text lessons');
            }
            $data['video_url'] = null;
        } else {
            throw new \Exception('Invalid lesson type');
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['type'])) {
            $data = $this->validateLessonContent($data['type'], $data);
        }

        $this->repository->findOrFail($id);

        return $this->repository->update($id, $data);
    }
}
