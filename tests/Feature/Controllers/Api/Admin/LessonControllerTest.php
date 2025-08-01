<?php

namespace Tests\Feature\Controllers\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Services\Interfaces\LessonServiceInterface;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    private LessonServiceInterface $lessonService;
    private ModuleServiceInterface $moduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lessonService = Mockery::mock(LessonServiceInterface::class);
        $this->moduleService = Mockery::mock(ModuleServiceInterface::class);
        $this->app->instance(LessonServiceInterface::class, $this->lessonService);
        $this->app->instance(ModuleServiceInterface::class, $this->moduleService);
    }

    public function test_index_returns_module_lessons()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $lessons = Lesson::factory(3)->make(['id' => rand(1, 100), 'module_id' => 1]);

        $this->lessonService
            ->expects('getLessonsByModule')
            ->with(1)
            ->andReturn($lessons);

        $response = $this->getJson('/api/admin/modules/1/lessons');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'order']
                ]
            ]);
    }

    public function test_store_creates_video_lesson()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $lessonData = [
            'title' => 'Video Lesson',
            'description' => 'Test Description',
            'type' => 'video',
            'video_url' => 'https://youtube.com/watch?v=123',
            'duration_minutes' => 30
        ];

        $lesson = Lesson::factory()->make(array_merge($lessonData, ['id' => 1, 'module_id' => 1]));

        $this->lessonService
            ->expects('createLesson')
            ->with(array_merge($lessonData, ['module_id' => 1]))
            ->andReturn($lesson);

        $response = $this->postJson('/api/admin/modules/1/lessons', $lessonData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Video Lesson'
                ]
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/modules/1/lessons', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'type']);
    }

    public function test_update_updates_lesson_content()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $updateData = ['title' => 'Updated Lesson'];
        $lesson = Lesson::factory()->make(['id' => 1, 'module_id' => 1]);
        $updatedLesson = Lesson::factory()->make(['id' => 1, 'module_id' => 1, 'title' => 'Updated Lesson']);

        $this->lessonService
            ->expects('findOrFail')
            ->with(1)
            ->twice()
            ->andReturn($lesson, $updatedLesson);

        $this->lessonService
            ->expects('update')
            ->with(1, $updateData)
            ->andReturn(true);

        $response = $this->putJson('/api/admin/modules/1/lessons/1', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'Updated Lesson'
                ]
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
