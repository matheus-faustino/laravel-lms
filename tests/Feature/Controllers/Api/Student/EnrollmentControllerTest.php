<?php

namespace Tests\Feature\Controllers\Api\Student;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\Interfaces\EnrollmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_index_returns_student_enrollments()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $enrollments = Enrollment::factory(5)->make(['id' => 1, 'student_id' => $student->id]);

        $this->enrollmentService
            ->expects('getStudentEnrollments')
            ->with($student->id)
            ->andReturn($enrollments);

        $response = $this->getJson('/api/student/enrollments');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => 1]
                ]
            ]);
    }

    public function test_store_enrolls_student_in_course()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        Sanctum::actingAs($student);

        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $this->enrollmentService
            ->expects('enrollStudent')
            ->with($student->id, $course->id)
            ->andReturn($enrollment);

        $enrollment->setRelation('course', $course);

        $response = $this->postJson('/api/student/enrollments', [
            'course_id' => $course->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'student_id' => $student->id,
                    'course_id' => $course->id
                ],
                'message' => 'Enrolled successfully'
            ]);
    }

    public function test_store_returns_error_when_enrollment_fails()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        Sanctum::actingAs($student);

        $this->enrollmentService
            ->expects('enrollStudent')
            ->with($student->id, $course->id)
            ->andThrow(new \Exception('Student cannot be enrolled in this course'));

        $response = $this->postJson('/api/student/enrollments', [
            'course_id' => $course->id
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Student cannot be enrolled in this course'
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->postJson('/api/student/enrollments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['course_id']);
    }

    public function test_show_returns_enrollment_for_authenticated_student()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $student->id
        ]);

        $this->enrollmentService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($enrollment);

        $enrollment->setRelation('course', Course::factory()->make());
        $enrollment->setRelation('progress', collect());

        $response = $this->getJson('/api/student/enrollments/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'student_id' => $student->id
                ]
            ]);
    }

    public function test_show_denies_access_to_other_student_enrollment()
    {
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $otherStudent->id
        ]);

        $this->enrollmentService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($enrollment);

        $response = $this->getJson('/api/student/enrollments/1');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Access denied'
            ]);
    }

    public function test_destroy_cancels_own_enrollment()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $student->id
        ]);

        $this->enrollmentService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($enrollment);

        $this->enrollmentService
            ->expects('cancelEnrollment')
            ->with(1)
            ->andReturn(true);

        $response = $this->deleteJson('/api/student/enrollments/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Enrollment cancelled successfully'
            ]);
    }

    public function test_destroy_denies_access_to_other_student_enrollment()
    {
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $enrollment = Enrollment::factory()->make([
            'id' => 1,
            'student_id' => $otherStudent->id
        ]);

        $this->enrollmentService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($enrollment);

        $response = $this->deleteJson('/api/student/enrollments/1');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Access denied'
            ]);
    }

    public function test_requires_student_role()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/student/enrollments');
        $response->assertStatus(403);

        $response = $this->postJson('/api/student/enrollments', []);
        $response->assertStatus(403);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/student/enrollments');
        $response->assertStatus(401);

        $response = $this->postJson('/api/student/enrollments', []);
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
