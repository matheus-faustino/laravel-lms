<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Repositories\CourseRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CourseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CourseRepository(new Course());
    }

    public function test_get_by_status_returns_courses_with_specific_status()
    {
        Course::factory()->create(['active' => true]);
        Course::factory(2)->create(['active' => false]);

        $activeCourses = $this->repository->getByStatus(true);
        $inactiveCourses = $this->repository->getByStatus(false);

        $this->assertCount(1, $activeCourses);
        $this->assertCount(2, $inactiveCourses);
    }

    public function test_search_courses_filters_by_search_term()
    {
        Course::factory()->create(['title' => 'Laravel Course', 'description' => 'Learn Laravel']);
        Course::factory()->create(['title' => 'React Course', 'description' => 'Learn React']);

        $results = $this->repository->searchCourses(['search' => 'Laravel']);

        $this->assertEquals(1, $results->total());
        $this->assertEquals('Laravel Course', $results->first()->title);
    }

    public function test_search_courses_filters_by_active_status()
    {
        Course::factory(2)->create(['active' => true]);
        Course::factory(3)->create(['active' => false]);

        $results = $this->repository->searchCourses(['active' => true]);

        $this->assertEquals(2, $results->total());
        $this->assertTrue($results->getCollection()->every(fn($course) => $course->active));
    }

    public function test_get_active_courses_returns_only_active_courses()
    {
        Course::factory(3)->create(['active' => true]);
        Course::factory(2)->create(['active' => false]);

        $activeCourses = $this->repository->getActiveCourses();

        $this->assertCount(3, $activeCourses);
        $this->assertTrue($activeCourses->every(fn($course) => $course->active));
    }

    public function test_get_courses_for_student_includes_enrollment_data()
    {
        $student = User::factory()->student()->create();
        $course1 = Course::factory()->active()->create();
        $course2 = Course::factory()->active()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course1->id,
            'progress_percentage' => 50.0,
            'enrolled_at' => now(),
            'active' => true
        ]);

        $results = $this->repository->getCoursesForStudent($student->id);

        $this->assertEquals(2, $results->total());

        $enrolledCourse = $results->getCollection()->firstWhere('id', $course1->id);
        $this->assertNotNull($enrolledCourse->enrollment_id);
        $this->assertEquals(50.0, $enrolledCourse->progress_percentage);
    }

    public function test_find_for_student_returns_course_with_enrollment_data()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->active()->create();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'progress_percentage' => 75.0,
            'enrolled_at' => now(),
            'active' => true
        ]);

        $result = $this->repository->findForStudent($course->id, $student->id);

        $this->assertNotNull($result);
        $this->assertEquals($course->id, $result->id);
        $this->assertNotNull($result->enrollment_id);
        $this->assertEquals($enrollment->id, $result->enrollment_id);
        $this->assertEquals(75.0, $result->progress_percentage);
    }

    public function test_find_for_student_returns_course_without_enrollment_when_not_enrolled()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->active()->create();

        $result = $this->repository->findForStudent($course->id, $student->id);

        $this->assertNotNull($result);
        $this->assertEquals($course->id, $result->id);
        $this->assertNull($result->enrollment_id);
        $this->assertNull($result->progress_percentage);
    }

    public function test_find_for_student_returns_null_for_inactive_course()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->inactive()->create();

        $result = $this->repository->findForStudent($course->id, $student->id);

        $this->assertNull($result);
    }
}
