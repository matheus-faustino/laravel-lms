<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct(private CourseServiceInterface $courseService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'enrolled']);
        $perPage = $request->get('per_page', 15);
        $studentId = Auth::id();

        $courses = $this->courseService->getCoursesForStudent($studentId, $filters, $perPage);

        return response()->json([
            'data' => $courses
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $studentId = Auth::id();
        $course = $this->courseService->getCourseForStudent($id, $studentId);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], 404);
        }

        $courseData = $course->toArray();
        $courseData['is_enrolled'] = !is_null($course->enrollment_id);
        $courseData['progress_percentage'] = $course->progress_percentage ?? 0;

        if ($course->modules) {
            foreach ($courseData['modules'] as &$module) {
                $module['lessons_count'] = count($module['lessons']);
            }
        }

        return response()->json([
            'data' => $courseData
        ]);
    }
}
