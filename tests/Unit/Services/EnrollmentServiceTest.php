<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Services\EnrollmentService;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentService $service;
    private EnrollmentRepositoryInterface $enrollmentRepository;
    private CourseRepositoryInterface $courseRepository;
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->enrollmentRepository = Mockery::mock(EnrollmentRepositoryInterface::class);
        $this->courseRepository = Mockery::mock(CourseRepositoryInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);

        $this->service = new EnrollmentService(
            $this->enrollmentRepository,
            $this->courseRepository,
            $this->userRepository
        );
    }

    public function test_enroll_student_creates_enrollment()
    {
        $student = User::factory()->student()->make(['id' => 1]);
        $course = Course::factory()->make(['id' => 1, 'active' => true]);
        $enrollment = Enrollment::factory()->make(['id' => 1]);

        $this->userRepository
            ->expects('find')
            ->with(1)
            ->andReturn($student);

        $this->courseRepository
            ->expects('find')
            ->with(1)
            ->andReturn($course);

        $this->enrollmentRepository
            ->expects('isEnrolled')
            ->with(1, 1)
            ->andReturn(false);

        $this->enrollmentRepository
            ->expects('create')
            ->with(Mockery::on(function ($data) {
                return $data['student_id'] === 1 &&
                    $data['course_id'] === 1 &&
                    $data['progress_percentage'] === 0.00 &&
                    $data['active'] === true;
            }))
            ->andReturn($enrollment);

        $result = $this->service->enrollStudent(1, 1);

        $this->assertEquals($enrollment, $result);
    }

    public function test_enroll_student_throws_exception_when_already_enrolled()
    {
        $student = User::factory()->student()->make(['id' => 1]);
        $course = Course::factory()->make(['id' => 1, 'active' => true]);

        $this->userRepository
            ->expects('find')
            ->with(1)
            ->andReturn($student);

        $this->courseRepository
            ->expects('find')
            ->with(1)
            ->andReturn($course);

        $this->enrollmentRepository
            ->expects('isEnrolled')
            ->with(1, 1)
            ->andReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Student cannot be enrolled in this course');

        $this->service->enrollStudent(1, 1);
    }

    public function test_can_enroll_returns_true_for_valid_enrollment()
    {
        $student = User::factory()->student()->make(['id' => 1]);
        $course = Course::factory()->make(['id' => 1, 'active' => true]);

        $this->userRepository
            ->expects('find')
            ->with(1)
            ->andReturn($student);

        $this->courseRepository
            ->expects('find')
            ->with(1)
            ->andReturn($course);

        $this->enrollmentRepository
            ->expects('isEnrolled')
            ->with(1, 1)
            ->andReturn(false);

        $result = $this->service->canEnroll(1, 1);

        $this->assertTrue($result);
    }

    public function test_can_enroll_returns_false_for_inactive_course()
    {
        $student = User::factory()->student()->make(['id' => 1]);
        $course = Course::factory()->make(['id' => 1, 'active' => false]);

        $this->userRepository
            ->expects('find')
            ->with(1)
            ->andReturn($student);

        $this->courseRepository
            ->expects('find')
            ->with(1)
            ->andReturn($course);

        $result = $this->service->canEnroll(1, 1);

        $this->assertFalse($result);
    }

    public function test_can_enroll_returns_false_for_non_student()
    {
        $admin = User::factory()->admin()->make(['id' => 1]);
        $course = Course::factory()->make(['id' => 1, 'active' => true]);

        $this->userRepository
            ->expects('find')
            ->with(1)
            ->andReturn($admin);

        $this->courseRepository
            ->expects('find')
            ->with(1)
            ->andReturn($course);

        $result = $this->service->canEnroll(1, 1);

        $this->assertFalse($result);
    }

    public function test_cancel_enrollment_updates_active_status()
    {
        $this->enrollmentRepository
            ->expects('update')
            ->with(1, ['active' => false])
            ->andReturn(true);

        $result = $this->service->cancelEnrollment(1);

        $this->assertTrue($result);
    }

    public function test_get_student_enrollments_delegates_to_repository()
    {
        $enrollments = Enrollment::factory(5)->make();

        $this->enrollmentRepository
            ->expects('getByStudent')
            ->with(1)
            ->andReturn($enrollments);

        $result = $this->service->getStudentEnrollments(1);

        $this->assertEquals($enrollments, $result);
    }

    public function test_get_course_enrollments_delegates_to_repository()
    {
        $enrollments = Enrollment::factory(5)->make();

        $this->enrollmentRepository
            ->expects('getByCourse')
            ->with(1)
            ->andReturn($enrollments);

        $result = $this->service->getCourseEnrollments(1);

        $this->assertEquals($enrollments, $result);
    }

    public function test_find_by_student_and_course_delegates_to_repository()
    {
        $enrollment = Enrollment::factory()->make();

        $this->enrollmentRepository
            ->expects('findByStudentAndCourse')
            ->with(1, 1)
            ->andReturn($enrollment);

        $result = $this->service->findByStudentAndCourse(1, 1);

        $this->assertEquals($enrollment, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
