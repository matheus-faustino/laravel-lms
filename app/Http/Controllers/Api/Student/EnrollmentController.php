<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreEnrollmentRequest;
use App\Services\Interfaces\EnrollmentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentServiceInterface $enrollmentService) {}

    public function index(Request $request): JsonResponse
    {
        $studentId = Auth::id();
        $enrollments = $this->enrollmentService->getStudentEnrollments($studentId);

        return response()->json([
            'data' => $enrollments
        ]);
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        try {
            $enrollment = $this->enrollmentService->enrollStudent(
                Auth::id(),
                $request->validated('course_id')
            );

            return response()->json([
                'data' => $enrollment->load(['course']),
                'message' => 'Enrolled successfully'
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

        if ($enrollment->student_id !== Auth::id()) {
            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $enrollment->load(['course', 'progress.lesson']);

        return response()->json([
            'data' => $enrollment
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $enrollment = $this->enrollmentService->findOrFail($id);

        if ($enrollment->student_id !== Auth::id()) {
            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $this->enrollmentService->cancelEnrollment($id);

        return response()->json([
            'message' => 'Enrollment cancelled successfully'
        ]);
    }
}
