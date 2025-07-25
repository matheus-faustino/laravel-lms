<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Course;
use App\Services\CourseService;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseService $service;
    private CourseRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(CourseRepositoryInterface::class);
        $this->service = new CourseService($this->repository);
    }

    public function test_get_active_courses_delegates_to_repository()
    {
        $courses = Course::factory(3)->make(['active' => true]);

        $this->repository
            ->expects('getActiveCourses')
            ->andReturn($courses);

        $result = $this->service->getActiveCourses();

        $this->assertEquals($courses, $result);
    }

    public function test_search_courses_delegates_to_repository()
    {
        $filters = ['search' => 'Laravel', 'active' => true];
        $perPage = 10;
        $mockPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->expects('searchCourses')
            ->with($filters, $perPage)
            ->andReturn($mockPaginator);

        $result = $this->service->searchCourses($filters, $perPage);

        $this->assertEquals($mockPaginator, $result);
    }

    public function test_toggle_status_changes_course_active_status()
    {
        $course = Course::factory()->make(['id' => 1, 'active' => true]);

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($course);

        $this->repository
            ->expects('update')
            ->with(1, ['active' => false])
            ->andReturn(true);

        $result = $this->service->toggleStatus(1);

        $this->assertTrue($result);
    }

    public function test_find_with_stats_delegates_to_repository()
    {
        $course = Course::factory()->make(['id' => 1]);

        $this->repository
            ->expects('findWithCounts')
            ->with(1)
            ->andReturn($course);

        $result = $this->service->findWithStats(1);

        $this->assertEquals($course, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
