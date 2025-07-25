<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Services\Interfaces\EnrollmentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentServiceInterface $enrollmentService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['course_id', 'student_id', 'status', 'search']);
        $perPage = $request->get('per_page', 15);

        $enrollments = $this->enrollmentService->searchEnrollments($filters, $perPage);

        return response()->json([
            'data' => $enrollments
        ]);
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        try {
            $enrollment = $this->enrollmentService->enrollStudent(
                $request->validated('student_id'),
                $request->validated('course_id')
            );

            return response()->json([
                'data' => $enrollment->load(['student', 'course']),
                'message' => 'Student enrolled successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $enrollment = $this->enrollmentService->findOrFail($id);
        $enrollment->load(['student', 'course', 'progress.lesson']);

        return response()->json([
            'data' => $enrollment
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->enrollmentService->cancelEnrollment($id);

        return response()->json([
            'message' => 'Enrollment cancelled successfully'
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = $this->enrollmentService->getEnrollmentStats();

        return response()->json([
            'data' => $stats
        ]);
    }
}
