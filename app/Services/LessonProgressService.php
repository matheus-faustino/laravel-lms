<?php

namespace App\Services;

use App\Enums\EnrollmentStatusEnum;
use App\Interfaces\Services\EnrollmentServiceInterface;
use App\Interfaces\Services\LessonProgressServiceInterface;
use App\Models\Course;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\DB;

class LessonProgressService implements LessonProgressServiceInterface
{
    public function __construct(private EnrollmentServiceInterface $enrollmentService) {}

    /** @inheritDoc */
    public function markAsWatched(int $userId, int $lessonId): LessonProgress
    {
        return DB::transaction(function () use ($userId, $lessonId): LessonProgress {
            $progress = LessonProgress::query()->firstOrCreate([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ]);

            $this->checkCourseCompletion($userId, $lessonId);

            return $progress;
        });
    }

    /** @inheritDoc */
    public function isLessonWatched(int $userId, int $lessonId): bool
    {
        return LessonProgress::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    /** @inheritDoc */
    public function getWatchedLessonIds(int $userId, int $courseId): array
    {
        return LessonProgress::query()
            ->where('user_id', $userId)
            ->whereHas('lesson.module.course', function ($query) use ($courseId) {
                $query->where('id', $courseId);
            })
            ->pluck('lesson_id')
            ->toArray();
    }

    /** @inheritDoc */
    public function getCourseProgress(int $userId, int $courseId): array
    {
        $course = Course::query()->find($courseId);

        if (! $course) {
            return ['total' => 0, 'watched' => 0, 'percentage' => 0.0];
        }

        $total = $course->lessons()->count();
        $watched = LessonProgress::query()
            ->where('user_id', $userId)
            ->whereHas('lesson.module', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->count();

        $percentage = $total > 0 ? round(($watched / $total) * 100, 2) : 0.0;

        return compact('total', 'watched', 'percentage');
    }

    private function checkCourseCompletion(int $userId, int $lessonId): void
    {
        $lesson = \App\Models\Lesson::query()->with('module.course')->find($lessonId);

        if (! $lesson?->module?->course) {
            return;
        }

        $courseId = $lesson->module->course->id;

        $progress = $this->getCourseProgress($userId, $courseId);

        if ($progress['watched'] >= $progress['total'] && $progress['total'] > 0) {
            $enrollment = $this->enrollmentService->getActiveEnrollment($userId, $courseId);

            if ($enrollment) {
                $this->enrollmentService->updateEnrollment($enrollment->id, [
                    'status' => EnrollmentStatusEnum::COMPLETED->value,
                ]);
            }
        }
    }
}
