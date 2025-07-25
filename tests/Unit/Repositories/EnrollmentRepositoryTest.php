<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Repositories\EnrollmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnrollmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EnrollmentRepository(new Enrollment());
    }

    public function test_get_by_student_returns_student_enrollments()
    {
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();

        Enrollment::factory(2)->create(['student_id' => $student->id]);
        Enrollment::factory()->create(['student_id' => $otherStudent->id]);

        $enrollments = $this->repository->getByStudent($student->id);

        $this->assertCount(2, $enrollments);
        $this->assertTrue($enrollments->every(fn($e) => $e->student_id === $student->id));
    }

    public function test_get_by_course_returns_course_enrollments()
    {
        $course = Course::factory()->create();
        $otherCourse = Course::factory()->create();

        Enrollment::factory(3)->create(['course_id' => $course->id]);
        Enrollment::factory()->create(['course_id' => $otherCourse->id]);

        $enrollments = $this->repository->getByCourse($course->id);

        $this->assertCount(3, $enrollments);
        $this->assertTrue($enrollments->every(fn($e) => $e->course_id === $course->id));
    }

    public function test_find_by_student_and_course_returns_specific_enrollment()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $found = $this->repository->findByStudentAndCourse($student->id, $course->id);

        $this->assertNotNull($found);
        $this->assertEquals($enrollment->id, $found->id);
    }

    public function test_is_enrolled_returns_true_when_active_enrollment_exists()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'active' => true
        ]);

        $isEnrolled = $this->repository->isEnrolled($student->id, $course->id);

        $this->assertTrue($isEnrolled);
    }

    public function test_is_enrolled_returns_false_when_no_active_enrollment()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'active' => false
        ]);

        $isEnrolled = $this->repository->isEnrolled($student->id, $course->id);

        $this->assertFalse($isEnrolled);
    }

    public function test_search_enrollments_filters_by_course_id()
    {
        $course = Course::factory()->create();
        Enrollment::factory(2)->create(['course_id' => $course->id]);
        Enrollment::factory()->create(); // Different course

        $results = $this->repository->searchEnrollments(['course_id' => $course->id]);

        $this->assertEquals(2, $results->total());
    }

    public function test_search_enrollments_filters_by_status()
    {
        Enrollment::factory()->completed()->create();
        Enrollment::factory()->inProgress()->create();
        Enrollment::factory()->create(['active' => false]);

        $completedResults = $this->repository->searchEnrollments(['status' => 'completed']);
        $activeResults = $this->repository->searchEnrollments(['status' => 'active']);
        $cancelledResults = $this->repository->searchEnrollments(['status' => 'cancelled']);

        $this->assertEquals(1, $completedResults->total());
        $this->assertEquals(1, $activeResults->total());
        $this->assertEquals(1, $cancelledResults->total());
    }

    public function test_get_active_enrollments_returns_only_active()
    {
        $student = User::factory()->student()->create();

        Enrollment::factory(2)->create([
            'student_id' => $student->id,
            'active' => true
        ]);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'active' => false
        ]);

        $activeEnrollments = $this->repository->getActiveEnrollments($student->id);

        $this->assertCount(2, $activeEnrollments);
        $this->assertTrue($activeEnrollments->every(fn($e) => $e->active));
    }

    public function test_get_completed_enrollments_returns_only_completed()
    {
        $student = User::factory()->student()->create();

        Enrollment::factory()->completed()->create(['student_id' => $student->id]);
        Enrollment::factory()->inProgress()->create(['student_id' => $student->id]);

        $completedEnrollments = $this->repository->getCompletedEnrollments($student->id);

        $this->assertCount(1, $completedEnrollments);
        $this->assertNotNull($completedEnrollments->first()->completed_at);
    }
}
