<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Http\Requests\Admin\Course\UpdateCourseRequest;
use App\Interfaces\Services\CourseServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseServiceInterface $courseService) {}

    public function index(Request $request): View
    {
        $courses = $this->courseService->getPaginatedCourses(
            $request->query('perPage'),
            [],
            ['id', 'title', 'status', 'created_at'],
        );

        return view('admin.course.index', compact('courses'));
    }

    public function create(): View
    {
        return view('admin.course.create');
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $this->courseService->createCourse($request->validated());

        return to_route('admin.courses.index')->with('success', __('admin/courses.created_success'));
    }

    public function edit(int $courseId): View
    {
        $course = $this->courseService->getCourse($courseId);

        return view('admin.course.edit', compact('course'));
    }

    public function update(int $courseId, UpdateCourseRequest $request): RedirectResponse
    {
        $this->courseService->updateCourse($courseId, $request->validated());

        return to_route('admin.courses.index')->with('success', __('admin/courses.updated_success'));
    }

    public function delete(int $courseId): JsonResponse
    {
        $this->courseService->deleteCourse($courseId);

        return response()->json(['message' => __('admin/courses.deleted_success')]);
    }

    public function publish(int $courseId): JsonResponse
    {
        $this->courseService->updateCourse($courseId, ['status' => CourseStatusEnum::PUBLISHED]);

        return response()->json(['message' => __('admin/courses.published_success')]);
    }

    public function preview(int $courseId): View
    {
        $course = $this->courseService->getCourse($courseId);

        abort_if(!$course, 404);

        $course->load('modules.lessons');

        return view('admin.course.preview', compact('course'));
    }
}
