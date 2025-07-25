<?php

namespace Tests\Feature\Controllers\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\Interfaces\EnrollmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Sanctum\Sanctum;
use Mockery;

class EnrollmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentServiceInterface $enrollmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollmentService = Mockery::mock(EnrollmentServiceInterface::class);
        $this->app->instance(EnrollmentServiceInterface::class, $this->enrollmentService);
    }

    public function test_index_returns_paginated_enrollments()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $enrollments = new LengthAwarePaginator(
            [Enrollment::factory()->make(['id' => 1])],
            1,
            15,
            1
        );

        $this->enrollmentService
            ->expects('searchEnrollments')
            ->with(['course_id' => '1'], 15)
            ->andReturn($enrollments);

        $response = $this->getJson('/api/admin/enrollments?course_id=1&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id']
                    ]
                ]
            ]);
    }

    public function test_store_creates_enrollment_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $this->enrollmentService
            ->expects('enrollStudent')
            ->with($student->id, $course->id)
            ->andReturn($enrollment);

        $enrollment->setRelation('student', $student);
        $enrollment->setRelation('course', $course);

        $response = $this->postJson('/api/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'student_id' => $student->id,
                    'course_id' => $course->id
                ],
                'message' => 'Student enrolled successfully'
            ]);
    }

    public function test_store_returns_error_when_enrollment_fails()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->enrollmentService
            ->expects('enrollStudent')
            ->with($student->id, $course->id)
            ->andThrow(new \Exception('Student cannot be enrolled in this course'));

        $response = $this->postJson('/api/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Student cannot be enrolled in this course'
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/enrollments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'course_id']);
    }

    public function test_show_returns_enrollment_details()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $enrollment = Enrollment::factory()->make(['id' => 1]);

        $this->enrollmentService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($enrollment);

        $enrollment->setRelation('student', User::factory()->make());
        $enrollment->setRelation('course', Course::factory()->make());
        $enrollment->setRelation('progress', collect());

        $response = $this->getJson('/api/admin/enrollments/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1
                ]
            ]);
    }

    public function test_destroy_cancels_enrollment()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->enrollmentService
            ->expects('cancelEnrollment')
            ->with(1)
            ->andReturn(true);

        $response = $this->deleteJson('/api/admin/enrollments/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Enrollment cancelled successfully'
            ]);
    }

    public function test_stats_returns_enrollment_statistics()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $stats = [
            'total' => 100,
            'active' => 75,
            'completed' => 20,
            'cancelled' => 5
        ];

        $this->enrollmentService
            ->expects('getEnrollmentStats')
            ->andReturn($stats);

        $response = $this->getJson('/api/admin/enrollments/stats');

        $response->assertStatus(200)
            ->assertJson([
                'data' => $stats
            ]);
    }

    public function test_requires_admin_role()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->getJson('/api/admin/enrollments');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/enrollments', []);
        $response->assertStatus(403);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/admin/enrollments');
        $response->assertStatus(401);

        $response = $this->postJson('/api/admin/enrollments', []);
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
