<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Http\Requests\Admin\UpdateEnrollmentRequest;
use App\Interfaces\Services\EnrollmentServiceInterface;
use App\Interfaces\Services\LessonProgressServiceInterface;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentServiceInterface $serviceInterface,
        private LessonProgressServiceInterface $progressService,
    ) {}

    public function index(Request $request): View
    {
        $enrollments = $this->serviceInterface->getPaginatedEnrollments(
            $request->query('perPage', 10),
        );

        $progressMap = [];
        foreach ($enrollments as $enrollment) {
            $progressMap[$enrollment->id] = $this->progressService->getCourseProgress(
                $enrollment->user_id,
                $enrollment->course_id,
            );
        }

        return view('admin.enrollment.index', compact('enrollments', 'progressMap'));
    }

    public function progress(int $enrollmentId): View
    {
        $enrollment = $this->serviceInterface->getEnrollment($enrollmentId);

        abort_if(! $enrollment, 404);

        $modules = Course::query()
            ->with(['modules' => fn ($q) => $q->orderBy('order')->with(['lessons' => fn ($q) => $q->orderBy('order')])])
            ->find($enrollment->course_id)
            ?->modules ?? collect();

        $watchedLessonIds = $this->progressService->getWatchedLessonIds(
            $enrollment->user_id,
            $enrollment->course_id,
        );

        $progress = $this->progressService->getCourseProgress(
            $enrollment->user_id,
            $enrollment->course_id,
        );

        return view('admin.enrollment.progress', compact('enrollment', 'modules', 'watchedLessonIds', 'progress'));
    }

    public function create(): View
    {
        $users = User::query()->users()->get(['id', 'name', 'email']);
        $courses = Course::query()->get(['id', 'title']);
        $statuses = EnrollmentStatusEnum::cases();

        return view('admin.enrollment.new', compact('users', 'courses', 'statuses'));
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $this->serviceInterface->createEnrollment($request->validated());

        return to_route('admin.enrollments.index')->with('success', __('admin/enrollments.created_success'));
    }

    public function edit(int $enrollmentId): View
    {
        $enrollment = $this->serviceInterface->getEnrollment($enrollmentId);

        abort_if(!$enrollment, 404);

        $users = User::query()->users()->get(['id', 'name', 'email']);
        $courses = Course::query()->get(['id', 'title']);
        $statuses = EnrollmentStatusEnum::cases();

        return view('admin.enrollment.edit', compact('enrollment', 'users', 'courses', 'statuses'));
    }

    public function update(UpdateEnrollmentRequest $request, int $enrollmentId): RedirectResponse
    {
        $this->serviceInterface->updateEnrollment($enrollmentId, $request->validated());

        return to_route('admin.enrollments.index')->with('success', __('admin/enrollments.updated_success'));
    }

    public function delete(int $enrollmentId): JsonResponse
    {
        $this->serviceInterface->deleteEnrollment($enrollmentId);

        return response()->json(['message' => __('admin/enrollments.deleted_success')]);
    }
}
