<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Module;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\ModuleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ModuleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ModuleRepository(new Module());
    }

    public function test_get_by_course_returns_modules_ordered()
    {
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id, 'order' => 3]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $modules = $this->repository->getByCourse($course->id);

        $this->assertCount(3, $modules);
        $this->assertEquals(1, $modules->first()->order);
        $this->assertEquals(3, $modules->last()->order);
    }

    public function test_find_with_lessons_includes_lessons()
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        Lesson::factory(3)->create(['module_id' => $module->id]);

        $result = $this->repository->findWithLessons($module->id);

        $this->assertNotNull($result);
        $this->assertCount(3, $result->lessons);
    }

    public function test_update_order_changes_module_order()
    {
        $module = Module::factory()->create(['order' => 1]);

        $result = $this->repository->updateOrder($module->id, 5);

        $this->assertTrue($result);
        $this->assertEquals(5, $module->fresh()->order);
    }

    public function test_get_next_order_returns_incremented_value()
    {
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id, 'order' => 3]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 1]);

        $nextOrder = $this->repository->getNextOrder($course->id);

        $this->assertEquals(4, $nextOrder);
    }

    public function test_get_next_order_returns_one_for_empty_course()
    {
        $course = Course::factory()->create();

        $nextOrder = $this->repository->getNextOrder($course->id);

        $this->assertEquals(1, $nextOrder);
    }

    public function test_reorder_after_deletion_decrements_higher_orders()
    {
        $course = Course::factory()->create();
        Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 3]);
        Module::factory()->create(['course_id' => $course->id, 'order' => 4]);

        $this->repository->reorderAfterDeletion($course->id, 2);

        $modules = Module::where('course_id', $course->id)->orderBy('order')->get();
        $this->assertCount(3, $modules);
        $this->assertEquals([1, 2, 3], $modules->pluck('order')->toArray());
    }
}
