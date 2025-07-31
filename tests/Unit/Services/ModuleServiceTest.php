<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Module;
use App\Services\ModuleService;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ModuleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ModuleService $service;
    private ModuleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ModuleRepositoryInterface::class);
        $this->service = new ModuleService($this->repository);
    }

    public function test_get_modules_by_course_delegates_to_repository()
    {
        $modules = Module::factory(3)->make();

        $this->repository
            ->expects('getByCourse')
            ->with(1)
            ->andReturn($modules);

        $result = $this->service->getModulesByCourse(1);

        $this->assertEquals($modules, $result);
    }

    public function test_create_module_adds_order_when_not_provided()
    {
        $data = ['course_id' => 1, 'title' => 'Test Module', 'description' => 'Test'];
        $module = Module::factory()->make($data);

        $this->repository
            ->expects('getNextOrder')
            ->with(1)
            ->andReturn(3);

        $this->repository
            ->expects('create')
            ->with(array_merge($data, ['order' => 3]))
            ->andReturn($module);

        $result = $this->service->createModule($data);

        $this->assertEquals($module, $result);
    }

    public function test_create_module_preserves_provided_order()
    {
        $data = ['course_id' => 1, 'title' => 'Test Module', 'description' => 'Test', 'order' => 5];
        $module = Module::factory()->make($data);

        $this->repository
            ->expects('create')
            ->with($data)
            ->andReturn($module);

        $result = $this->service->createModule($data);

        $this->assertEquals($module, $result);
    }

    public function test_find_with_lessons_delegates_to_repository()
    {
        $module = Module::factory()->make(['id' => 1]);

        $this->repository
            ->expects('findWithLessons')
            ->with(1)
            ->andReturn($module);

        $result = $this->service->findWithLessons(1);

        $this->assertEquals($module, $result);
    }

    public function test_update_order_delegates_to_repository()
    {
        $this->repository
            ->expects('updateOrder')
            ->with(1, 5)
            ->andReturn(true);

        $result = $this->service->updateOrder(1, 5);

        $this->assertTrue($result);
    }

    public function test_delete_module_reorders_after_deletion()
    {
        $module = Module::factory()->make(['id' => 1, 'course_id' => 2, 'order' => 3]);

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($module);

        $this->repository
            ->expects('delete')
            ->with(1)
            ->andReturn(true);

        $this->repository
            ->expects('reorderAfterDeletion')
            ->with(2, 3);

        $result = $this->service->deleteModule(1);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
