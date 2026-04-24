<?php

namespace Tests\Feature\Admin;

use App\Enums\CourseStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('courseGetRoutes')]
    public function test_admin_can_access_course_get_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)->get(route($routeName))->assertOk();
    }

    #[DataProvider('courseGetRoutes')]
    public function test_user_cant_access_course_get_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->get(route($routeName))->assertForbidden();
    }

    #[DataProvider('courseGetRoutes')]
    public function test_unauthenticated_cant_access_course_get_routes(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    public static function courseGetRoutes(): array
    {
        return [
            'index'  => ['admin.courses.index'],
            'create' => ['admin.courses.create'],
        ];
    }

    public function test_admin_can_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)->get(route('admin.courses.edit', $course->id))->assertOk();
    }

    public function test_user_cant_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user   = User::factory()->create(['role' => RoleEnum::USER]);
        $course = Course::factory()->create();

        $this->actingAs($user)->get(route('admin.courses.edit', $course->id))->assertForbidden();
    }

    public function test_unauthenticated_cant_access_edit_route(): void
    {
        $course = Course::factory()->create();

        $this->get(route('admin.courses.edit', $course->id))->assertRedirect(route('login'));
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
            'store'   => ['admin.courses.store',   'post',   []],
            'update'  => ['admin.courses.update',  'put',    ['courseId' => 999]],
            'delete'  => ['admin.courses.delete',  'delete', ['courseId' => 999]],
            'publish' => ['admin.courses.publish', 'patch',  ['courseId' => 999]],
        ];
    }

    public function test_index_returns_paginated_courses(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        Course::factory(15)->create();

        $this->actingAs($admin)
            ->get(route('admin.courses.index', ['perPage' => 10]))
            ->assertOk()
            ->assertViewIs('admin.course.index')
            ->assertViewHas('courses', function (LengthAwarePaginator $courses) {
                $items = $courses->items();

                $this->assertCount(10, $items);
                $this->assertSame(15, $courses->total());
                $this->assertSame(['id', 'title', 'status', 'created_at'], array_keys($items[0]->getAttributes()));

                return true;
            });
    }

    public function test_create_returns_correct_view(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.courses.create'))
            ->assertOk()
            ->assertViewIs('admin.course.create');
    }

    public function test_admin_can_create_course(): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $payload = [
            'title'       => 'Laravel From Scratch',
            'description' => 'A full beginner course on Laravel.',
            'thumbnail'   => UploadedFile::fake()->image('thumbnail.jpg'),
            'banner'      => UploadedFile::fake()->image('banner.jpg'),
        ];

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), $payload)
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success', __('admin/courses.created_success'));

        $this->assertDatabaseHas('courses', ['title' => 'Laravel From Scratch']);
    }

    #[DataProvider('invalidStorePayloads')]
    public function test_create_course_with_invalid_data_returns_validation_error(array $override, array $expectedErrors): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $payload = array_merge([
            'title'       => 'Valid Title',
            'description' => 'Valid description.',
            'thumbnail'   => UploadedFile::fake()->image('thumbnail.jpg'),
            'banner'      => UploadedFile::fake()->image('banner.jpg'),
        ], $override);

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidStorePayloads(): array
    {
        return [
            'missing title'           => [['title' => ''],                                                                     ['title']],
            'title too long'          => [['title' => str_repeat('a', 256)],                                                  ['title']],
            'missing description'     => [['description' => ''],                                                               ['description']],
            'missing thumbnail'       => [['thumbnail' => null],                                                               ['thumbnail']],
            'invalid thumbnail'       => [['thumbnail' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],    ['thumbnail']],
            'thumbnail too large'     => [['thumbnail' => UploadedFile::fake()->image('thumbnail.jpg', 601, 401)],             ['thumbnail']],
            'missing banner'          => [['banner' => null],                                                                  ['banner']],
            'invalid banner'          => [['banner' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],       ['banner']],
            'banner too large'        => [['banner' => UploadedFile::fake()->image('banner.jpg', 1921, 601)],                  ['banner']],
        ];
    }

    public function test_admin_can_access_edit_view_with_correct_data(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.courses.edit', $course->id))
            ->assertOk()
            ->assertViewIs('admin.course.edit')
            ->assertViewHas('course', fn(Course $c) => $c->is($course));
    }

    public function test_admin_can_update_course(): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->draft()->create(['title' => 'Old Title']);

        $payload = [
            'title'       => 'New Title',
            'description' => 'Updated description.',
        ];

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course->id), $payload)
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success', __('admin/courses.updated_success'));

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'New Title']);
        $this->assertDatabaseMissing('courses', ['id' => $course->id, 'title' => 'Old Title']);
    }

    public function test_admin_can_update_course_images(): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $payload = [
            'title'     => $course->title,
            'thumbnail' => UploadedFile::fake()->image('new-thumb.jpg'),
            'banner'    => UploadedFile::fake()->image('new-banner.jpg'),
        ];

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course->id), $payload)
            ->assertRedirect(route('admin.courses.index'));
    }

    #[DataProvider('invalidUpdatePayloads')]
    public function test_update_course_with_invalid_data_returns_validation_error(array $override, array $expectedErrors): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $payload = array_merge(['title' => 'Valid Title'], $override);

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course->id), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidUpdatePayloads(): array
    {
        return [
            'missing title'       => [['title' => ''],                                                                     ['title']],
            'title too long'      => [['title' => str_repeat('a', 256)],                                                  ['title']],
            'invalid thumbnail'   => [['thumbnail' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],   ['thumbnail']],
            'thumbnail too large' => [['thumbnail' => UploadedFile::fake()->image('thumbnail.jpg', 601, 401)],             ['thumbnail']],
            'invalid banner'      => [['banner'    => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],   ['banner']],
            'banner too large'    => [['banner'    => UploadedFile::fake()->image('banner.jpg', 1921, 601)],               ['banner']],
        ];
    }

    public function test_update_nonexistent_course_returns_not_found(): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->put(route('admin.courses.update', ['courseId' => 999]), ['title' => 'Valid Title'])
            ->assertNotFound();
    }

    public function test_admin_can_delete_course(): void
    {
        Storage::fake('public');

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.courses.delete', $course->id))
            ->assertOk()
            ->assertJsonPath('message', __('admin/courses.deleted_success'));

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_delete_nonexistent_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.delete', ['courseId' => 999]))
            ->assertNotFound();
    }

    public function test_admin_can_publish_a_draft_course(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin  = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $course = Course::factory()->draft()->create();

        $this->actingAs($admin)
            ->patch(route('admin.courses.publish', $course->id))
            ->assertOk()
            ->assertJsonPath('message', __('admin/courses.published_success'));

        $this->assertDatabaseHas('courses', [
            'id'     => $course->id,
            'status' => CourseStatusEnum::PUBLISHED->value,
        ]);
    }

    public function test_publish_nonexistent_course_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->patch(route('admin.courses.publish', ['courseId' => 999]))
            ->assertNotFound();
    }
}
