<?php

namespace Tests\Feature\Admin;

use App\Enums\LessonTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    private function textLesson(): array
    {
        return [
            'title'       => 'Introduction',
            'description' => 'First lesson',
            'type'        => LessonTypeEnum::TEXT->value,
            'duration'    => 300,
            'content'     => 'Some content here',
        ];
    }

    private function videoLesson(): array
    {
        return [
            'title'       => 'Video Lesson',
            'description' => 'Watch this',
            'type'        => LessonTypeEnum::VIDEO->value,
            'duration'    => 420,
            'youtube_id'  => 'dQw4w9WgXcQ',
        ];
    }

    public function test_admin_can_access_lesson_index(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertOk();
    }

    public function test_user_cant_access_lesson_index(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($user)
            ->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertForbidden();
    }

    public function test_unauthenticated_cant_access_lesson_index(): void
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertRedirect(route('login'));
    }

    public function test_index_with_nonexistent_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', [$course->id, 999]))
            ->assertNotFound();
    }

    public function test_index_with_module_from_different_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course      = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $module      = Module::factory()->create(['course_id' => $otherCourse->id]);

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertNotFound();
    }

    public function test_index_returns_correct_view_with_module_and_lessons(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
        Lesson::factory()->create(['module_id' => $module->id, 'order' => 2]);

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertOk()
            ->assertViewIs('admin.lesson.index')
            ->assertViewHas('module', fn(Module $m) => $m->is($module))
            ->assertViewHas('lessons', fn($lessons) => $lessons->count() === 2);
    }

    public function test_index_returns_lessons_ordered_by_order_column(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $third  = Lesson::factory()->create(['module_id' => $module->id, 'order' => 3]);
        $first  = Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
        $second = Lesson::factory()->create(['module_id' => $module->id, 'order' => 2]);

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', [$course->id, $module->id]))
            ->assertViewHas('lessons', function ($lessons) use ($first, $second, $third) {
                $this->assertTrue($lessons[0]->is($first));
                $this->assertTrue($lessons[1]->is($second));
                $this->assertTrue($lessons[2]->is($third));
                return true;
            });
    }

    #[DataProvider('mutationRoutes')]
    public function test_user_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->{$method}(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('mutationRoutes')]
    public function test_unauthenticated_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        $this->{$method}(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function mutationRoutes(): array
    {
        return [
            'store'   => ['admin.lessons.store',   'post',   ['courseId' => 999, 'moduleId' => 999]],
            'update'  => ['admin.lessons.update',  'put',    ['courseId' => 999, 'moduleId' => 999, 'lessonId' => 999]],
            'delete'  => ['admin.lessons.delete',  'delete', ['courseId' => 999, 'moduleId' => 999, 'lessonId' => 999]],
            'reorder' => ['admin.lessons.reorder', 'post',   ['courseId' => 999, 'moduleId' => 999]],
        ];
    }

    public function test_admin_can_create_text_lesson(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.store', [$course->id, $module->id]), $this->textLesson())
            ->assertCreated()
            ->assertJsonPath('title', 'Introduction');

        $this->assertDatabaseHas('lessons', ['title' => 'Introduction', 'module_id' => $module->id]);
    }

    public function test_admin_can_create_video_lesson(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.store', [$course->id, $module->id]), $this->videoLesson())
            ->assertCreated()
            ->assertJsonPath('title', 'Video Lesson');

        $this->assertDatabaseHas('lessons', ['title' => 'Video Lesson', 'module_id' => $module->id]);
    }

    public function test_store_auto_assigns_next_order(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
        Lesson::factory()->create(['module_id' => $module->id, 'order' => 2]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.store', [$course->id, $module->id]), $this->textLesson())
            ->assertCreated()
            ->assertJsonPath('order', 3);
    }

    public function test_store_with_missing_fields_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.store', [$course->id, $module->id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'description', 'type', 'duration']);
    }

    public function test_store_with_nonexistent_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.store', [$course->id, 999]), $this->textLesson())
            ->assertNotFound();
    }

    public function test_admin_can_update_lesson(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->text()->create(['module_id' => $module->id, 'order' => 1, 'title' => 'Old Title']);

        $payload = array_merge($this->textLesson(), ['title' => 'New Title']);

        $this->actingAs($admin)
            ->putJson(route('admin.lessons.update', [$course->id, $module->id, $lesson->id]), $payload)
            ->assertOk()
            ->assertJsonPath('title', 'New Title');

        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'New Title']);
    }

    public function test_update_lesson_belonging_to_different_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course      = Course::factory()->create();
        $module      = Module::factory()->create(['course_id' => $course->id]);
        $otherModule = Module::factory()->create(['course_id' => $course->id]);
        $lesson      = Lesson::factory()->text()->create(['module_id' => $otherModule->id, 'order' => 1]);

        $this->actingAs($admin)
            ->putJson(route('admin.lessons.update', [$course->id, $module->id, $lesson->id]), $this->textLesson())
            ->assertNotFound();
    }

    public function test_update_with_missing_fields_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->text()->create(['module_id' => $module->id, 'order' => 1]);

        $this->actingAs($admin)
            ->putJson(route('admin.lessons.update', [$course->id, $module->id, $lesson->id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'description', 'type', 'duration']);
    }

    public function test_admin_can_delete_lesson(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.lessons.delete', [$course->id, $module->id, $lesson->id]))
            ->assertOk()
            ->assertJsonPath('message', __('admin/lessons.deleted_success'));

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_delete_reorders_remaining_lessons(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $first  = Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
        $middle = Lesson::factory()->create(['module_id' => $module->id, 'order' => 2]);
        $last   = Lesson::factory()->create(['module_id' => $module->id, 'order' => 3]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.lessons.delete', [$course->id, $module->id, $middle->id]))
            ->assertOk();

        $this->assertDatabaseHas('lessons', ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas('lessons', ['id' => $last->id,  'order' => 2]);
        $this->assertDatabaseMissing('lessons', ['id' => $middle->id]);
    }

    public function test_delete_nonexistent_lesson_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.lessons.delete', [$course->id, $module->id, 999]))
            ->assertNotFound();
    }

    public function test_delete_lesson_belonging_to_different_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course      = Course::factory()->create();
        $module      = Module::factory()->create(['course_id' => $course->id]);
        $otherModule = Module::factory()->create(['course_id' => $course->id]);
        $lesson      = Lesson::factory()->create(['module_id' => $otherModule->id, 'order' => 1]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.lessons.delete', [$course->id, $module->id, $lesson->id]))
            ->assertNotFound();
    }

    public function test_admin_can_reorder_lessons(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $first  = Lesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
        $second = Lesson::factory()->create(['module_id' => $module->id, 'order' => 2]);
        $third  = Lesson::factory()->create(['module_id' => $module->id, 'order' => 3]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.reorder', [$course->id, $module->id]), [
                'lessons' => [$third->id, $first->id, $second->id],
            ])
            ->assertOk()
            ->assertJsonPath('message', __('admin/lessons.reordered_success'));

        $this->assertDatabaseHas('lessons', ['id' => $third->id,  'order' => 1]);
        $this->assertDatabaseHas('lessons', ['id' => $first->id,  'order' => 2]);
        $this->assertDatabaseHas('lessons', ['id' => $second->id, 'order' => 3]);
    }

    public function test_reorder_with_nonexistent_lesson_id_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.reorder', [$course->id, $module->id]), [
                'lessons' => [999],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lessons.0']);
    }

    public function test_reorder_with_nonexistent_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.lessons.reorder', [$course->id, 999]), ['lessons' => []])
            ->assertNotFound();
    }
}
