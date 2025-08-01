<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLessonRequest;
use App\Http\Requests\Admin\UpdateLessonRequest;
use App\Http\Requests\Admin\ReorderLessonRequest;
use App\Services\Interfaces\LessonServiceInterface;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    public function __construct(
        private LessonServiceInterface $lessonService,
        private ModuleServiceInterface $moduleService
    ) {}

    public function index(int $moduleId): JsonResponse
    {
        $lessons = $this->lessonService->getLessonsByModule($moduleId);

        return response()->json([
            'data' => $lessons
        ]);
    }

    public function store(StoreLessonRequest $request, int $moduleId): JsonResponse
    {
        try {
            $data = array_merge($request->validated(), ['module_id' => $moduleId]);
            $lesson = $this->lessonService->createLesson($data);

            return response()->json([
                'data' => $lesson
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(int $moduleId, int $id): JsonResponse
    {
        $lesson = $this->lessonService->findOrFail($id);

        if ($lesson->module_id !== $moduleId) {
            return response()->json([
                'message' => 'Lesson not found'
            ], 404);
        }

        return response()->json([
            'data' => $lesson
        ]);
    }

    public function update(UpdateLessonRequest $request, int $moduleId, int $id): JsonResponse
    {
        try {
            $lesson = $this->lessonService->findOrFail($id);

            if ($lesson->module_id !== $moduleId) {
                return response()->json([
                    'message' => 'Lesson not found'
                ], 404);
            }

            $this->lessonService->update($id, $request->validated());
            $updatedLesson = $this->lessonService->findOrFail($id);

            return response()->json([
                'data' => $updatedLesson
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(int $moduleId, int $id): JsonResponse
    {
        $lesson = $this->lessonService->findOrFail($id);

        if ($lesson->module_id !== $moduleId) {
            return response()->json([
                'message' => 'Lesson not found'
            ], 404);
        }

        $this->lessonService->deleteLesson($id);

        return response()->json([
            'message' => 'Lesson deleted successfully'
        ]);
    }

    public function reorder(ReorderLessonRequest $request, int $moduleId, int $id): JsonResponse
    {
        $lesson = $this->lessonService->findOrFail($id);

        if ($lesson->module_id !== $moduleId) {
            return response()->json([
                'message' => 'Lesson not found'
            ], 404);
        }

        $this->lessonService->updateOrder($id, $request->validated('order'));
        $updatedLesson = $this->lessonService->findOrFail($id);

        return response()->json([
            'data' => $updatedLesson,
            'message' => 'Lesson order updated successfully'
        ]);
    }
}
