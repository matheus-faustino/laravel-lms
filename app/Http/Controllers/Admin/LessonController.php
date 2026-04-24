<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLessonRequest;
use App\Http\Requests\Admin\UpdateLessonRequest;
use App\Interfaces\Services\LessonServiceInterface;
use App\Interfaces\Services\ModuleServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(
        private LessonServiceInterface $lessonService,
        private ModuleServiceInterface $moduleService,
    ) {}

    public function index(int $courseId, int $moduleId): View
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $lessons = $module->lessons()->orderBy('order')->get();

        return view('admin.lesson.index', compact('module', 'lessons'));
    }

    public function store(int $courseId, int $moduleId, StoreLessonRequest $request): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $nextOrder = $module->lessons()->max('order') + 1;

        $lesson = $this->lessonService->createLesson([
            ...$request->validated(),
            'order'     => $nextOrder,
            'module_id' => $moduleId,
        ]);

        return response()->json($lesson, 201);
    }

    public function update(int $courseId, int $moduleId, int $lessonId, UpdateLessonRequest $request): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $lesson = $this->lessonService->getLesson($lessonId);
        abort_if(!$lesson || $lesson->module_id !== $moduleId, 404);

        $lesson = $this->lessonService->updateLesson($lessonId, $request->validated());

        return response()->json($lesson);
    }

    public function delete(int $courseId, int $moduleId, int $lessonId): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $lesson = $this->lessonService->getLesson($lessonId);
        abort_if(!$lesson || $lesson->module_id !== $moduleId, 404);

        $this->lessonService->deleteLesson($lessonId);

        $remaining = $module->lessons()
            ->get(['id', 'order'])
            ->values()
            ->map(fn($l, int $i) => ['id' => $l->id, 'order' => $i + 1])
            ->all();

        if (!empty($remaining)) {
            $this->lessonService->updateOrder($remaining);
        }

        return response()->json(['message' => __('admin/lessons.deleted_success')]);
    }

    public function reorder(int $courseId, int $moduleId, Request $request): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $request->validate([
            'lessons'   => 'required|array',
            'lessons.*' => 'integer|exists:lessons,id',
        ]);

        $reindexed = collect($request->lessons)
            ->map(fn(int $id, int $index) => ['id' => $id, 'order' => $index + 1])
            ->all();

        $this->lessonService->updateOrder($reindexed);

        return response()->json(['message' => __('admin/lessons.reordered_success')]);
    }
}
