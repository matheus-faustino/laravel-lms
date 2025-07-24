<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function __construct(private CourseServiceInterface $courseService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'active']);
        $perPage = $request->get('per_page', 15);

        $courses = $this->courseService->searchCourses($filters, $perPage);

        return response()->json([
            'data' => $courses
        ]);
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = $this->courseService->create($request->validated());

        return response()->json([
            'data' => $course
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $course = $this->courseService->findWithStats($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'data' => $course
        ]);
    }

    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        $this->courseService->update($id, $request->validated());

        $course = $this->courseService->findOrFail($id);

        return response()->json([
            'data' => $course
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->courseService->delete($id);

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $this->courseService->toggleStatus($id);

        $course = $this->courseService->findOrFail($id);

        return response()->json([
            'data' => $course,
            'message' => 'Course status updated successfully'
        ]);
    }
}
