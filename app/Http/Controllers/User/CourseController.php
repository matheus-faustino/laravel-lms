<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\CourseServiceInterface;
use App\Interfaces\Services\EnrollmentServiceInterface;

class CourseController extends Controller
{
    public function __construct(private CourseServiceInterface $courseService, private EnrollmentServiceInterface $enrollmentService) {}

    public function index()
    {
        $courses = $this->courseService->getPaginatedEnrolledCourses(auth()->id());

        return view('user.course.index', compact('courses'));
    }

    public function show(int $courseId)
    {
        $course = $this->courseService->getCourse($courseId);

        abort_if(! $course, 404);

        $enrollment = $this->enrollmentService->getActiveEnrollment(auth()->id(), $courseId);

        abort_if(! $enrollment, 403);

        $course->load('modules.lessons');

        return view('user.course.show', compact('course', 'enrollment'));
    }
}
