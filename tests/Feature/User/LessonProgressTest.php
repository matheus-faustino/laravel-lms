<?php

namespace Tests\Feature\User;

use App\Enums\EnrollmentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTest extends TestCase
{
    use RefreshDatabase;

    private function createCourseWithLesson(): array
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['module_id' => $module->id]);

        return [$course, $lesson];
    }

    private function enrollUser(User $user, Course $course): void
    {
        Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_user_can_mark_lesson_as_watched(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        [$course, $lesson] = $this->createCourseWithLesson();
        $this->enrollUser($user, $course);

        $this->actingAs($user)
            ->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
            ->assertOk()
            ->assertJsonPath('watched', true)
            ->assertJsonPath('progress.total', 1)
            ->assertJsonPath('progress.watched', 1)
            ->assertJsonPath('progress.percentage', 100);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_admin_cannot_mark_lesson_as_watched(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        [$course, $lesson] = $this->createCourseWithLesson();

        $this->actingAs($admin)
            ->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_mark_lesson_as_watched(): void
    {
        [$course, $lesson] = $this->createCourseWithLesson();

        $this->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
            ->assertUnauthorized();
    }

    public function test_watch_endpoint_returns_progress_data(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(3)->create(['module_id' => $module->id]);
        $this->enrollUser($user, $course);

        $response = $this->actingAs($user)
            ->postJson(route('user.progress.watch', [$course->id, $lessons[1]->id]));

        $response->assertOk()
            ->assertJsonStructure([
                'watched',
                'progress' => ['total', 'watched', 'percentage'],
            ])
            ->assertJsonPath('progress.total', 3)
            ->assertJsonPath('progress.watched', 1);
    }

    public function test_marking_already_watched_lesson_is_idempotent(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        [$course, $lesson] = $this->createCourseWithLesson();
        $this->enrollUser($user, $course);

        $this->actingAs($user)
            ->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
            ->assertOk();

        $this->actingAs($user)
            ->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
            ->assertOk();

        $this->assertDatabaseCount('lesson_progress', 1);
    }

    public function test_mark_all_lessons_completes_enrollment(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lessons = Lesson::factory()->count(2)->create(['module_id' => $module->id]);
        $this->enrollUser($user, $course);

        foreach ($lessons as $lesson) {
            $this->actingAs($user)
                ->postJson(route('user.progress.watch', [$course->id, $lesson->id]))
                ->assertOk();
        }

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatusEnum::COMPLETED->value,
        ]);
    }
}
