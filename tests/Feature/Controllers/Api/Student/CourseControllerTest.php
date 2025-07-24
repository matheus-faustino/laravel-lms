<?php

namespace Tests\Feature\Controllers\Api\Student;

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

    public function test_index_returns_courses_for_student()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $courses = new LengthAwarePaginator(
            [
                (object) [
                    'id' => 1,
                    'title' => 'Laravel Course',
                    'enrollment_id' => 1,
                    'progress_percentage' => 50.0
                ]
            ],
            1,
            15,
            1
        );

        $this->courseService
            ->expects('getCoursesForStudent')
            ->with($student->id, ['search' => 'Laravel'], 15)
            ->andReturn($courses);

        $response = $this->getJson('/api/student/courses?search=Laravel&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'title']
                    ]
                ]
            ]);
    }

    public function test_show_returns_course_details_for_student()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $course = Course::factory()->make([
            'id' => 1,
            'title' => 'Laravel Course'
        ]);
        $course->enrollment_id = 1;
        $course->progress_percentage = 75.0;
        $course->modules = [];

        $this->courseService
            ->expects('getCourseForStudent')
            ->with(1, $student->id)
            ->andReturn($course);

        $response = $this->getJson('/api/student/courses/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Laravel Course',
                    'is_enrolled' => true,
                    'progress_percentage' => 75.0
                ]
            ]);
    }

    public function test_show_returns_404_when_course_not_found()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $this->courseService
            ->expects('getCourseForStudent')
            ->with(999, $student->id)
            ->andReturn(null);

        $response = $this->getJson('/api/student/courses/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Course not found']);
    }

    public function test_requires_student_role()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/student/courses');
        $response->assertStatus(403);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/student/courses');
        $response->assertStatus(401);

        $response = $this->getJson('/api/student/courses/1');
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
