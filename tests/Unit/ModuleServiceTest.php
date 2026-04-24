<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModuleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ModuleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ModuleService();
    }

    public function test_get_all_modules_returns_collection_of_all_modules(): void
    {
        Module::factory()->count(3)->create();

        $modules = $this->service->getAllModules();

        $this->assertCount(3, $modules);
    }

    public function test_get_all_modules_returns_only_selected_columns(): void
    {
        Module::factory()->count(2)->create();

        $results = $this->service->getAllModules(['id', 'title']);

        $attributes = $results->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('order', $attributes);
    }

    public function test_get_module_returns_correct_module_by_id(): void
    {
        $module = Module::factory()->create();

        $result = $this->service->getModule($module->id);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertEquals($module->id, $result->id);
    }

    public function test_get_module_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getModule(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_module_returns_only_selected_columns(): void
    {
        $module = Module::factory()->create();

        $result = $this->service->getModule($module->id, ['id', 'title']);

        $this->assertInstanceOf(Module::class, $result);
        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('order', $attributes);
    }

    public function test_create_module_persists_and_returns_module(): void
    {
        $result = $this->service->createModule(['title' => 'Introduction', 'order' => 1]);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertDatabaseHas('modules', ['title' => 'Introduction']);
    }

    public function test_create_module_with_course_reference_persists_course_id(): void
    {
        $course = Course::factory()->create();

        $result = $this->service->createModule([
            'title' => 'Getting Started',
            'order' => 1,
            'course_id' => $course->id,
        ]);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertDatabaseHas('modules', [
            'title' => 'Getting Started',
            'course_id' => $course->id,
        ]);
    }

    public function test_update_module_modifies_attributes_and_returns_fresh_model(): void
    {
        $module = Module::factory()->create(['title' => 'Old Title']);

        $updated = $this->service->updateModule($module->id, ['title' => 'New Title']);

        $this->assertInstanceOf(Module::class, $updated);
        $this->assertEquals('New Title', $updated->title);
        $this->assertDatabaseHas('modules', ['id' => $module->id, 'title' => 'New Title']);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent module' => ['updateModule', [PHP_INT_MAX, ['title' => 'test']]],
            'delete nonexistent module' => ['deleteModule', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_delete_module_removes_record_and_returns_true(): void
    {
        $module = Module::factory()->create();

        $result = $this->service->deleteModule($module->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }

    public function test_get_paginated_modules_returns_paginator_instance(): void
    {
        Module::factory()->count(5)->create();

        $result = $this->service->getPaginatedModules(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
    }

    public function test_get_paginated_modules_respects_per_page_limit(): void
    {
        Module::factory()->count(10)->create();

        $result = $this->service->getPaginatedModules(3);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(3, $result->items());
        $this->assertEquals(10, $result->total());
    }

    public function test_get_paginated_modules_filters_by_criteria(): void
    {
        $course = Course::factory()->create();
        Module::factory()->count(4)->create(['course_id' => $course->id]);
        Module::factory()->count(2)->create();

        $result = $this->service->getPaginatedModules(10, ['course_id' => $course->id]);

        $this->assertCount(4, $result->items());
    }

    public function test_get_paginated_modules_returns_only_selected_columns(): void
    {
        Module::factory()->count(3)->create();

        $result = $this->service->getPaginatedModules(10, [], ['id', 'title']);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $attributes = $result->items()[0]->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('order', $attributes);
    }

    public function test_update_order_reorders_modules(): void
    {
        $moduleA = Module::factory()->create(['order' => 1]);
        $moduleB = Module::factory()->create(['order' => 2]);

        $result = $this->service->updateOrder([
            ['id' => $moduleA->id, 'order' => 2],
            ['id' => $moduleB->id, 'order' => 1],
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('modules', ['id' => $moduleA->id, 'order' => 2]);
        $this->assertDatabaseHas('modules', ['id' => $moduleB->id, 'order' => 1]);
    }
}
