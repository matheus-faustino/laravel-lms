<?php

namespace Tests\Feature\Controllers\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Module;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    private ModuleServiceInterface $moduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleService = Mockery::mock(ModuleServiceInterface::class);
        $this->app->instance(ModuleServiceInterface::class, $this->moduleService);
    }

    public function test_index_returns_course_modules()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $modules = Module::factory(3)->make(['id' => rand(1, 100), 'course_id' => 1]);

        $this->moduleService
            ->expects('getModulesByCourse')
            ->with(1)
            ->andReturn($modules);

        $response = $this->getJson('/api/admin/courses/1/modules');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'order']
                ]
            ]);
    }

    public function test_store_creates_module_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $moduleData = [
            'title' => 'Test Module',
            'description' => 'Test Description',
            'order' => 1
        ];

        $module = Module::factory()->make(array_merge($moduleData, ['id' => 1, 'course_id' => 1]));

        $this->moduleService
            ->expects('createModule')
            ->with(array_merge($moduleData, ['course_id' => 1]))
            ->andReturn($module);

        $response = $this->postJson('/api/admin/courses/1/modules', $moduleData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Test Module'
                ]
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/courses/1/modules', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }

    public function test_show_returns_module_with_lessons()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $module = Module::factory()->make(['id' => 1, 'course_id' => 1]);

        $this->moduleService
            ->expects('findWithLessons')
            ->with(1)
            ->andReturn($module);

        $response = $this->getJson('/api/admin/courses/1/modules/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'course_id' => 1
                ]
            ]);
    }

    public function test_show_returns_404_when_module_not_found()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->moduleService
            ->expects('findWithLessons')
            ->with(999)
            ->andReturn(null);

        $response = $this->getJson('/api/admin/courses/1/modules/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Module not found']);
    }

    public function test_show_returns_404_for_wrong_course()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $module = Module::factory()->make(['id' => 1, 'course_id' => 2]);

        $this->moduleService
            ->expects('findWithLessons')
            ->with(1)
            ->andReturn($module);

        $response = $this->getJson('/api/admin/courses/1/modules/1');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Module not found']);
    }

    public function test_update_updates_module_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $updateData = ['title' => 'Updated Module'];
        $module = Module::factory()->make(['id' => 1, 'course_id' => 1]);
        $updatedModule = Module::factory()->make(['id' => 1, 'course_id' => 1, 'title' => 'Updated Module']);

        $this->moduleService
            ->expects('findOrFail')
            ->with(1)
            ->twice()
            ->andReturn($module, $updatedModule);

        $this->moduleService
            ->expects('update')
            ->with(1, $updateData)
            ->andReturn(true);

        $response = $this->putJson('/api/admin/courses/1/modules/1', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Updated Module'
                ]
            ]);
    }

    public function test_destroy_deletes_module()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $module = Module::factory()->make(['id' => 1, 'course_id' => 1]);

        $this->moduleService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($module);

        $this->moduleService
            ->expects('deleteModule')
            ->with(1)
            ->andReturn(true);

        $response = $this->deleteJson('/api/admin/courses/1/modules/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Module deleted successfully'
            ]);
    }

    public function test_reorder_updates_module_order()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $module = Module::factory()->make(['id' => 1, 'course_id' => 1, 'order' => 1]);
        $reorderedModule = Module::factory()->make(['id' => 1, 'course_id' => 1, 'order' => 3]);

        $this->moduleService
            ->expects('findOrFail')
            ->with(1)
            ->twice()
            ->andReturn($module, $reorderedModule);

        $this->moduleService
            ->expects('updateOrder')
            ->with(1, 3)
            ->andReturn(true);

        $response = $this->putJson('/api/admin/courses/1/modules/1/reorder', ['order' => 3]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => ['id' => 1, 'order' => 3],
                'message' => 'Module order updated successfully'
            ]);
    }

    public function test_requires_admin_role()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->getJson('/api/admin/courses/1/modules');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/courses/1/modules', []);
        $response->assertStatus(403);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/admin/courses/1/modules');
        $response->assertStatus(401);

        $response = $this->postJson('/api/admin/courses/1/modules', []);
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
