<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateModuleRequest;
use App\Interfaces\Services\CourseServiceInterface;
use App\Interfaces\Services\ModuleServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function __construct(
        private ModuleServiceInterface $moduleService,
        private CourseServiceInterface $courseService,
    ) {}

    public function index(int $courseId): View
    {
        $course = $this->courseService->getCourse($courseId);
        abort_if(!$course, 404);

        $modules = $course->modules;

        return view('admin.module.index', compact('course', 'modules'));
    }

    public function store(int $courseId, Request $request): JsonResponse
    {
        $course = $this->courseService->getCourse($courseId);
        abort_if(!$course, 404);

        $validated = $request->validate(['title' => 'required|max:255']);

        $nextOrder = $course->modules()->max('order') + 1;

        $module = $this->moduleService->createModule([
            'title'     => $validated['title'],
            'order'     => $nextOrder,
            'course_id' => $courseId,
        ]);

        return response()->json($module, 201);
    }

    public function update(int $courseId, int $moduleId, UpdateModuleRequest $request): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $module = $this->moduleService->updateModule($moduleId, $request->validated());

        return response()->json($module);
    }

    public function delete(int $courseId, int $moduleId): JsonResponse
    {
        $module = $this->moduleService->getModule($moduleId);
        abort_if(!$module || $module->course_id !== $courseId, 404);

        $this->moduleService->deleteModule($moduleId);

        $remaining = $this->courseService->getCourse($courseId)
            ->modules()
            ->get(['id', 'order'])
            ->values()
            ->map(fn($m, int $i) => ['id' => $m->id, 'order' => $i + 1])
            ->all();

        if (!empty($remaining)) {
            $this->moduleService->updateOrder($remaining);
        }

        return response()->json(['message' => __('admin/modules.deleted_success')]);
    }

    public function reorder(int $courseId, Request $request): JsonResponse
    {
        $course = $this->courseService->getCourse($courseId);
        abort_if(!$course, 404);

        $request->validate([
            'modules'   => 'required|array',
            'modules.*' => 'integer|exists:modules,id',
        ]);

        $reindexed = collect($request->modules)
            ->map(fn(int $id, int $index) => ['id' => $id, 'order' => $index + 1])
            ->all();

        $this->moduleService->updateOrder($reindexed);

        return response()->json(['message' => __('admin/modules.reordered_success')]);
    }
}
