<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreModuleRequest;
use App\Http\Requests\Admin\UpdateModuleRequest;
use App\Http\Requests\Admin\ReorderModuleRequest;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function __construct(private ModuleServiceInterface $moduleService) {}

    public function index(int $courseId): JsonResponse
    {
        $modules = $this->moduleService->getModulesByCourse($courseId);

        return response()->json([
            'data' => $modules
        ]);
    }

    public function store(StoreModuleRequest $request, int $courseId): JsonResponse
    {
        $data = array_merge($request->validated(), ['course_id' => $courseId]);
        $module = $this->moduleService->createModule($data);

        return response()->json([
            'data' => $module
        ], 201);
    }

    public function show(int $courseId, int $id): JsonResponse
    {
        $module = $this->moduleService->findWithLessons($id);

        if (!$module || $module->course_id !== $courseId) {
            return response()->json([
                'message' => 'Module not found'
            ], 404);
        }

        return response()->json([
            'data' => $module
        ]);
    }

    public function update(UpdateModuleRequest $request, int $courseId, int $id): JsonResponse
    {
        $module = $this->moduleService->findOrFail($id);

        if ($module->course_id !== $courseId) {
            return response()->json([
                'message' => 'Module not found'
            ], 404);
        }

        $this->moduleService->update($id, $request->validated());
        $updatedModule = $this->moduleService->findOrFail($id);

        return response()->json([
            'data' => $updatedModule
        ]);
    }

    public function destroy(int $courseId, int $id): JsonResponse
    {
        $module = $this->moduleService->findOrFail($id);

        if ($module->course_id !== $courseId) {
            return response()->json([
                'message' => 'Module not found'
            ], 404);
        }

        $this->moduleService->deleteModule($id);

        return response()->json([
            'message' => 'Module deleted successfully'
        ]);
    }

    public function reorder(ReorderModuleRequest $request, int $courseId, int $id): JsonResponse
    {
        $module = $this->moduleService->findOrFail($id);

        if ($module->course_id !== $courseId) {
            return response()->json([
                'message' => 'Module not found'
            ], 404);
        }

        $this->moduleService->updateOrder($id, $request->validated('order'));
        $updatedModule = $this->moduleService->findOrFail($id);

        return response()->json([
            'data' => $updatedModule,
            'message' => 'Module order updated successfully'
        ]);
    }
}