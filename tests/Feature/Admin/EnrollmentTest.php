<?php

namespace Tests\Feature\Admin;

use App\Enums\EnrollmentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('getRoutes')]
    public function test_admin_can_access_get_routes(string $routeName, ?array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)->get(route($routeName, $params))->assertOk();
    }

    #[DataProvider('getRoutes')]
    public function test_regular_user_cannot_access_get_routes(string $routeName, ?array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->get(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('getRoutes')]
    public function test_unauthenticated_user_cannot_access_get_routes(string $routeName, ?array $params = []): void
    {
        $this->get(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function getRoutes(): array
    {
        return [
            'index'  => ['admin.enrollments.index'],
            'create' => ['admin.enrollments.create'],
        ];
    }

    public function test_admin_can_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin      = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.edit', ['enrollmentId' => $enrollment->id]))
            ->assertOk();
    }

    public function test_regular_user_cannot_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user       = User::factory()->create(['role' => RoleEnum::USER]);
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.enrollments.edit', ['enrollmentId' => $enrollment->id]))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_access_edit_route(): void
    {
        $this->get(route('admin.enrollments.edit', ['enrollmentId' => 1]))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('mutationRoutes')]
    public function test_regular_user_cannot_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->{$method}(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('mutationRoutes')]
    public function test_unauthenticated_user_cannot_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        $this->{$method}(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function mutationRoutes(): array
    {
        return [
            'store'  => ['admin.enrollments.store',  'post',   []],
            'update' => ['admin.enrollments.update', 'put',    ['enrollmentId' => 999]],
            'delete' => ['admin.enrollments.delete', 'delete', ['enrollmentId' => 999]],
        ];
    }

    public function test_index_returns_paginated_enrollments(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        Enrollment::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertViewIs('admin.enrollment.index')
            ->assertViewHas('enrollments', function (LengthAwarePaginator $enrollments) {
                $this->assertCount(3, $enrollments->items());

                return true;
            });
    }

    public function test_index_respects_per_page_query_param(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        Enrollment::factory()->count(5)->create();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index', ['perPage' => 2]))
            ->assertOk()
            ->assertViewHas('enrollments', function (LengthAwarePaginator $enrollments) {
                $this->assertCount(2, $enrollments->items());
                $this->assertEquals(5, $enrollments->total());

                return true;
            });
    }

    public function test_create_view_contains_users_courses_and_statuses(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        User::factory()->count(2)->create(['role' => RoleEnum::USER]);
        Course::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.create'))
            ->assertOk()
            ->assertViewIs('admin.enrollment.new')
            ->assertViewHas('users')
            ->assertViewHas('courses')
            ->assertViewHas('statuses');
    }

    public function test_create_view_exposes_only_regular_users(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin       = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $regularUser = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.create'))
            ->assertViewHas('users', function ($users) use ($admin, $regularUser) {
                $ids = $users->pluck('id')->all();
                $this->assertContains($regularUser->id, $ids);
                $this->assertNotContains($admin->id, $ids);

                return true;
            });
    }

    public function test_admin_can_create_enrollment(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $payload = [
            'status'    => EnrollmentStatusEnum::ACTIVE->value,
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ];

        $this->actingAs($admin)
            ->post(route('admin.enrollments.store'), $payload)
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('success', __('admin/enrollments.created_success'));

        $this->assertDatabaseHas('enrollments', [
            'status'    => EnrollmentStatusEnum::ACTIVE->value,
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);
    }

    #[DataProvider('invalidStorePayloads')]
    public function test_store_with_invalid_data_returns_validation_errors(array $override, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $payload = array_merge([
            'status'    => EnrollmentStatusEnum::ACTIVE->value,
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ], $override);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.store'), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidStorePayloads(): array
    {
        return [
            'missing status'        => [['status' => ''],       ['status']],
            'missing user_id'       => [['user_id' => ''],      ['user_id']],
            'nonexistent user_id'   => [['user_id' => 99999],   ['user_id']],
            'missing course_id'     => [['course_id' => ''],    ['course_id']],
            'nonexistent course_id' => [['course_id' => 99999], ['course_id']],
        ];
    }

    public function test_edit_view_contains_enrollment_users_courses_and_statuses(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin      = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.edit', ['enrollmentId' => $enrollment->id]))
            ->assertOk()
            ->assertViewIs('admin.enrollment.edit')
            ->assertViewHas('enrollment', function (Enrollment $viewEnrollment) use ($enrollment) {
                return $viewEnrollment->is($enrollment);
            })
            ->assertViewHas('users')
            ->assertViewHas('courses')
            ->assertViewHas('statuses');
    }

    public function test_edit_nonexistent_enrollment_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.edit', ['enrollmentId' => 999]))
            ->assertNotFound();
    }

    public function test_admin_can_update_enrollment(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin      = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $enrollment = Enrollment::factory()->active()->create();
        $newCourse  = Course::factory()->create();
        $newUser    = User::factory()->create(['role' => RoleEnum::USER]);

        $payload = [
            'status'    => EnrollmentStatusEnum::COMPLETED->value,
            'user_id'   => $newUser->id,
            'course_id' => $newCourse->id,
        ];

        $this->actingAs($admin)
            ->put(route('admin.enrollments.update', ['enrollmentId' => $enrollment->id]), $payload)
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('success', __('admin/enrollments.updated_success'));

        $this->assertDatabaseHas('enrollments', [
            'id'        => $enrollment->id,
            'status'    => EnrollmentStatusEnum::COMPLETED->value,
            'user_id'   => $newUser->id,
            'course_id' => $newCourse->id,
        ]);
    }

    #[DataProvider('invalidUpdatePayloads')]
    public function test_update_with_invalid_data_returns_validation_errors(array $override, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin      = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $enrollment = Enrollment::factory()->create();
        $user       = User::factory()->create(['role' => RoleEnum::USER]);
        $course     = Course::factory()->create();

        $payload = array_merge([
            'status'    => EnrollmentStatusEnum::ACTIVE->value,
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ], $override);

        $this->actingAs($admin)
            ->put(route('admin.enrollments.update', ['enrollmentId' => $enrollment->id]), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidUpdatePayloads(): array
    {
        return [
            'missing status'        => [['status' => ''],       ['status']],
            'missing user_id'       => [['user_id' => ''],      ['user_id']],
            'nonexistent user_id'   => [['user_id' => 99999],   ['user_id']],
            'missing course_id'     => [['course_id' => ''],    ['course_id']],
            'nonexistent course_id' => [['course_id' => 99999], ['course_id']],
        ];
    }

    public function test_update_nonexistent_enrollment_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $payload = [
            'status'    => EnrollmentStatusEnum::ACTIVE->value,
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ];

        $this->actingAs($admin)
            ->put(route('admin.enrollments.update', ['enrollmentId' => 999]), $payload)
            ->assertNotFound();
    }

    public function test_admin_can_delete_enrollment(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin      = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.delete', ['enrollmentId' => $enrollment->id]))
            ->assertOk()
            ->assertJsonPath('message', __('admin/enrollments.deleted_success'));

        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_delete_nonexistent_enrollment_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.delete', ['enrollmentId' => 999]))
            ->assertNotFound();
    }
}
