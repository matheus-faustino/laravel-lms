<?php

namespace Tests\Feature\User;

use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_index_route(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->published()->create();
        Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.courses.index'))
            ->assertOk()
            ->assertViewIs('user.course.index')
            ->assertViewHas('courses');
    }

    public function test_admin_cant_access_index_route(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->get(route('user.courses.index'))
            ->assertForbidden();
    }

    public function test_unauthenticated_cant_access_index_route(): void
    {
        $this->get(route('user.courses.index'))
            ->assertRedirect(route('login'));
    }

    public function test_index_shows_only_enrolled_courses(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $anotherUser = User::factory()->create(['role' => RoleEnum::USER]);
        $enrolledCourse = Course::factory()->published()->create();
        $otherCourse = Course::factory()->published()->create();

        Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $enrolledCourse->id,
        ]);
        Enrollment::factory()->active()->create([
            'user_id' => $anotherUser->id,
            'course_id' => $otherCourse->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.courses.index'))
            ->assertOk()
            ->assertViewHas('courses', function ($courses) use ($enrolledCourse, $otherCourse) {
                return $courses->contains(fn (Course $c) => $c->is($enrolledCourse))
                    && ! $courses->contains(fn (Course $c) => $c->is($otherCourse));
            });
    }

    public function test_index_shows_empty_state_when_no_enrollments(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)
            ->get(route('user.courses.index'))
            ->assertOk()
            ->assertViewIs('user.course.index')
            ->assertViewHas('courses', function ($courses) {
                return $courses->isEmpty();
            });
    }

    public function test_user_can_access_show_route(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->active()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.courses.show', $course->id))
            ->assertOk()
            ->assertViewIs('user.course.show')
            ->assertViewHas('course', fn (Course $c) => $c->is($course))
            ->assertViewHas('enrollment', fn (Enrollment $e) => $e->is($enrollment));
    }

    public function test_show_returns_403_when_not_enrolled(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $this->actingAs($user)
            ->get(route('user.courses.show', $course->id))
            ->assertForbidden();
    }

    public function test_show_returns_403_when_enrollment_is_cancelled(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();
        Enrollment::factory()->cancelled()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.courses.show', $course->id))
            ->assertForbidden();
    }

    public function test_admin_cant_access_show_route(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->get(route('user.courses.show', $course->id))
            ->assertForbidden();
    }

    public function test_unauthenticated_cant_access_show_route(): void
    {
        $course = Course::factory()->create();

        $this->get(route('user.courses.show', $course->id))
            ->assertRedirect(route('login'));
    }

    public function test_show_returns_404_for_nonexistent_course(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)
            ->get(route('user.courses.show', 999))
            ->assertNotFound();
    }
}
