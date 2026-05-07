<?php

namespace Tests\Unit;

use App\Enums\EnrollmentStatusEnum;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Services\LessonProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonProgressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonProgressService(new EnrollmentService());
    }

    public function test_mark_as_watched_creates_progress_record(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $result = $this->service->markAsWatched($user->id, $lesson->id);

        $this->assertInstanceOf(LessonProgress::class, $result);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_mark_as_watched_is_idempotent(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->service->markAsWatched($user->id, $lesson->id);
        $this->service->markAsWatched($user->id, $lesson->id);

        $this->assertDatabaseCount('lesson_progress', 1);
    }

    public function test_is_lesson_watched_returns_true_for_watched_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->service->markAsWatched($user->id, $lesson->id);

        $this->assertTrue($this->service->isLessonWatched($user->id, $lesson->id));
    }

    public function test_is_lesson_watched_returns_false_for_unwatched_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->assertFalse($this->service->isLessonWatched($user->id, $lesson->id));
    }

    public function test_get_watched_lesson_ids_returns_correct_ids(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessonA = Lesson::factory()->create(['module_id' => $module->id]);
        $lessonB = Lesson::factory()->create(['module_id' => $module->id]);
        $lessonC = Lesson::factory()->create(['module_id' => $module->id]);

        $this->service->markAsWatched($user->id, $lessonA->id);
        $this->service->markAsWatched($user->id, $lessonC->id);

        $watchedIds = $this->service->getWatchedLessonIds($user->id, $course->id);

        $this->assertContains($lessonA->id, $watchedIds);
        $this->assertContains($lessonC->id, $watchedIds);
        $this->assertNotContains($lessonB->id, $watchedIds);
        $this->assertCount(2, $watchedIds);
    }

    public function test_get_watched_lesson_ids_filters_by_course(): void
    {
        $user = User::factory()->create();
        $courseA = Course::factory()->create();
        $courseB = Course::factory()->create();
        $moduleA = Module::factory()->create(['course_id' => $courseA->id]);
        $moduleB = Module::factory()->create(['course_id' => $courseB->id]);
        $lessonA = Lesson::factory()->create(['module_id' => $moduleA->id]);
        $lessonB = Lesson::factory()->create(['module_id' => $moduleB->id]);

        $this->service->markAsWatched($user->id, $lessonA->id);
        $this->service->markAsWatched($user->id, $lessonB->id);

        $watchedIdsA = $this->service->getWatchedLessonIds($user->id, $courseA->id);

        $this->assertContains($lessonA->id, $watchedIdsA);
        $this->assertNotContains($lessonB->id, $watchedIdsA);
        $this->assertCount(1, $watchedIdsA);
    }

    public function test_get_course_progress_returns_zero_for_nonexistent_course(): void
    {
        $result = $this->service->getCourseProgress(1, 999);

        $this->assertEquals([
            'total' => 0,
            'watched' => 0,
            'percentage' => 0.0,
        ], $result);
    }

    public function test_get_course_progress_returns_correct_stats(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(5)->create(['module_id' => $module->id]);

        $this->service->markAsWatched($user->id, $lessons[0]->id);
        $this->service->markAsWatched($user->id, $lessons[1]->id);

        $progress = $this->service->getCourseProgress($user->id, $course->id);

        $this->assertEquals(5, $progress['total']);
        $this->assertEquals(2, $progress['watched']);
        $this->assertEquals(40.0, $progress['percentage']);
    }

    public function test_get_course_progress_returns_100_when_all_lessons_watched(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(2)->create(['module_id' => $module->id]);

        foreach ($lessons as $lesson) {
            $this->service->markAsWatched($user->id, $lesson->id);
        }

        $progress = $this->service->getCourseProgress($user->id, $course->id);

        $this->assertEquals(2, $progress['total']);
        $this->assertEquals(2, $progress['watched']);
        $this->assertEquals(100.0, $progress['percentage']);
    }

    public function test_get_course_progress_returns_zero_when_course_has_no_lessons(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id]);

        $progress = $this->service->getCourseProgress($user->id, $course->id);

        $this->assertEquals(0, $progress['total']);
        $this->assertEquals(0, $progress['watched']);
        $this->assertEquals(0.0, $progress['percentage']);
    }

    public function test_mark_as_watched_completes_enrollment_when_all_lessons_watched(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(3)->create(['module_id' => $module->id]);
        $enrollment = Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        foreach ($lessons as $lesson) {
            $this->service->markAsWatched($user->id, $lesson->id);
        }

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => EnrollmentStatusEnum::COMPLETED->value,
        ]);
    }

    public function test_mark_as_watched_does_not_complete_enrollment_when_lessons_remain(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(3)->create(['module_id' => $module->id]);
        $enrollment = Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->service->markAsWatched($user->id, $lessons[0]->id);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => EnrollmentStatusEnum::ACTIVE->value,
        ]);
    }

    public function test_mark_as_watched_does_not_fail_without_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(2)->create(['module_id' => $module->id]);

        foreach ($lessons as $lesson) {
            $result = $this->service->markAsWatched($user->id, $lesson->id);
            $this->assertInstanceOf(LessonProgress::class, $result);
        }

        $this->assertDatabaseCount('lesson_progress', 2);
        $this->assertDatabaseCount('enrollments', 0);
    }
}
