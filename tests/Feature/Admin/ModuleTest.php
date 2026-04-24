<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_module_index(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.modules.index', $course->id))
            ->assertOk();
    }

    public function test_user_cant_access_module_index(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.modules.index', $course->id))
            ->assertForbidden();
    }

    public function test_unauthenticated_cant_access_module_index(): void
    {
        $course = Course::factory()->create();

        $this->get(route('admin.modules.index', $course->id))
            ->assertRedirect(route('login'));
    }

    public function test_index_with_nonexistent_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.modules.index', ['courseId' => 999]))
            ->assertNotFound();
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
            'store'   => ['admin.modules.store',   'post',   ['courseId' => 999]],
            'update'  => ['admin.modules.update',  'put',    ['courseId' => 999, 'moduleId' => 999]],
            'delete'  => ['admin.modules.delete',  'delete', ['courseId' => 999, 'moduleId' => 999]],
            'reorder' => ['admin.modules.reorder', 'post',   ['courseId' => 999]],
        ];
    }

    public function test_index_returns_correct_view_with_course_and_modules(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $this->actingAs($admin)
            ->get(route('admin.modules.index', $course->id))
            ->assertOk()
            ->assertViewIs('admin.module.index')
            ->assertViewHas('course', fn(Course $c) => $c->is($course))
            ->assertViewHas('modules', fn($modules) => $modules->count() === 2);
    }

    public function test_index_returns_modules_ordered_by_order_column(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $third  = Module::factory()->create(['course_id' => $course->id, 'order' => 3]);
        $first  = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $second = Module::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $this->actingAs($admin)
            ->get(route('admin.modules.index', $course->id))
            ->assertViewHas('modules', function ($modules) use ($first, $second, $third) {
                $this->assertTrue($modules[0]->is($first));
                $this->assertTrue($modules[1]->is($second));
                $this->assertTrue($modules[2]->is($third));
                return true;
            });
    }

    public function test_admin_can_create_module(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.modules.store', $course->id), ['title' => 'Introduction'])
            ->assertCreated()
            ->assertJsonPath('title', 'Introduction');

        $this->assertDatabaseHas('modules', ['title' => 'Introduction', 'course_id' => $course->id]);
    }

    public function test_store_auto_assigns_next_order(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $this->actingAs($admin)
            ->postJson(route('admin.modules.store', $course->id), ['title' => 'Third Module'])
            ->assertCreated()
            ->assertJsonPath('order', 3);
    }

    public function test_store_with_missing_title_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.modules.store', $course->id), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_store_with_nonexistent_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->postJson(route('admin.modules.store', ['courseId' => 999]), ['title' => 'Test'])
            ->assertNotFound();
    }

    public function test_admin_can_update_module_title(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id, 'order' => 1, 'title' => 'Old Title']);

        $this->actingAs($admin)
            ->putJson(route('admin.modules.update', [$course->id, $module->id]), ['title' => 'New Title'])
            ->assertOk()
            ->assertJsonPath('title', 'New Title');

        $this->assertDatabaseHas('modules', ['id' => $module->id, 'title' => 'New Title']);
    }

    public function test_update_module_belonging_to_different_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course      = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $module      = Module::factory()->create(['course_id' => $otherCourse->id, 'order' => 1]);

        $this->actingAs($admin)
            ->putJson(route('admin.modules.update', [$course->id, $module->id]), ['title' => 'Hacked'])
            ->assertNotFound();
    }

    public function test_update_with_missing_title_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);

        $this->actingAs($admin)
            ->putJson(route('admin.modules.update', [$course->id, $module->id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_admin_can_delete_module(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.modules.delete', [$course->id, $module->id]))
            ->assertOk()
            ->assertJsonPath('message', __('admin/modules.deleted_success'));

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }

    public function test_delete_reorders_remaining_modules(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $first  = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $middle = Module::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $last   = Module::factory()->create(['course_id' => $course->id, 'order' => 3]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.modules.delete', [$course->id, $middle->id]))
            ->assertOk();

        $this->assertDatabaseHas('modules', ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas('modules', ['id' => $last->id,  'order' => 2]);
        $this->assertDatabaseMissing('modules', ['id' => $middle->id]);
    }

    public function test_delete_module_belonging_to_different_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course      = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $module      = Module::factory()->create(['course_id' => $otherCourse->id, 'order' => 1]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.modules.delete', [$course->id, $module->id]))
            ->assertNotFound();
    }

    public function test_delete_nonexistent_module_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->deleteJson(route('admin.modules.delete', [$course->id, 999]))
            ->assertNotFound();
    }

    public function test_admin_can_reorder_modules(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();
        $first  = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $second = Module::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $third  = Module::factory()->create(['course_id' => $course->id, 'order' => 3]);

        // New order: third, first, second
        $this->actingAs($admin)
            ->postJson(route('admin.modules.reorder', $course->id), [
                'modules' => [$third->id, $first->id, $second->id],
            ])
            ->assertOk()
            ->assertJsonPath('message', __('admin/modules.reordered_success'));

        $this->assertDatabaseHas('modules', ['id' => $third->id,  'order' => 1]);
        $this->assertDatabaseHas('modules', ['id' => $first->id,  'order' => 2]);
        $this->assertDatabaseHas('modules', ['id' => $second->id, 'order' => 3]);
    }

    public function test_reorder_with_nonexistent_module_id_returns_unprocessable(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.modules.reorder', $course->id), [
                'modules' => [999],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['modules.0']);
    }

    public function test_reorder_with_nonexistent_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->postJson(route('admin.modules.reorder', ['courseId' => 999]), ['modules' => []])
            ->assertNotFound();
    }
}
