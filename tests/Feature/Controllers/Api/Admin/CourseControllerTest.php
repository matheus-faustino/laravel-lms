<?php

namespace Tests\Feature\Controllers\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Sanctum\Sanctum;
use Mockery;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    private CourseServiceInterface $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseService = Mockery::mock(CourseServiceInterface::class);
        $this->app->instance(CourseServiceInterface::class, $this->courseService);
    }

    public function test_index_returns_paginated_courses()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $courses = new LengthAwarePaginator(
            [Course::factory()->make(['id' => 1])],
            1,
            15,
            1
        );

        $this->courseService
            ->expects('searchCourses')
            ->with(['search' => 'Laravel'], 15)
            ->andReturn($courses);

        $response = $this->getJson('/api/admin/courses?search=Laravel&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'active']
                    ]
                ]
            ]);
    }

    public function test_store_creates_course_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $courseData = [
            'title' => 'Laravel Course',
            'description' => 'Learn Laravel framework',
            'duration_hours' => 40,
            'active' => true
        ];

        $course = Course::factory()->make($courseData);
        $course->id = 1;

        $this->courseService
            ->expects('create')
            ->with($courseData)
            ->andReturn($course);

        $response = $this->postJson('/api/admin/courses', $courseData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Laravel Course'
                ]
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/courses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }

    public function test_show_returns_course_with_stats()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $course = Course::factory()->make(['id' => 1]);

        $this->courseService
            ->expects('findWithStats')
            ->with(1)
            ->andReturn($course);

        $response = $this->getJson('/api/admin/courses/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1
                ]
            ]);
    }

    public function test_show_returns_404_when_course_not_found()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->courseService
            ->expects('findWithStats')
            ->with(999)
            ->andReturn(null);

        $response = $this->getJson('/api/admin/courses/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Course not found']);
    }

    public function test_update_updates_course_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $updateData = [
            'title' => 'Updated Course Title',
            'active' => false
        ];

        $updatedCourse = Course::factory()->make([
            'id' => 1,
            'title' => 'Updated Course Title',
            'active' => false
        ]);

        $this->courseService
            ->expects('update')
            ->with(1, $updateData)
            ->andReturn(true);

        $this->courseService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($updatedCourse);

        $response = $this->putJson('/api/admin/courses/1', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Updated Course Title'
                ]
            ]);
    }

    public function test_destroy_deletes_course()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->courseService
            ->expects('delete')
            ->with(1)
            ->andReturn(true);

        $response = $this->deleteJson('/api/admin/courses/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Course deleted successfully'
            ]);
    }

    public function test_toggle_status_changes_course_status()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $course = Course::factory()->make(['id' => 1, 'active' => false]);

        $this->courseService
            ->expects('toggleStatus')
            ->with(1)
            ->andReturn(true);

        $this->courseService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($course);

        $response = $this->putJson('/api/admin/courses/1/toggle-status');

        $response->assertStatus(200)
            ->assertJson([
                'data' => ['id' => 1],
                'message' => 'Course status updated successfully'
            ]);
    }

    public function test_requires_admin_role()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->getJson('/api/admin/courses');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/courses', []);
        $response->assertStatus(403);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
