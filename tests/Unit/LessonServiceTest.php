<?php

namespace Tests\Unit;

use App\Enums\LessonTypeEnum;
use App\Models\Lesson;
use App\Models\Module;
use App\Services\LessonService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LessonServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonService();
    }

    public function test_get_all_lessons_returns_collection_of_all_lessons(): void
    {
        Lesson::factory()->count(3)->create();

        $lessons = $this->service->getAllLessons();

        $this->assertCount(3, $lessons);
    }

    public function test_get_all_lessons_returns_only_selected_columns(): void
    {
        Lesson::factory()->count(2)->create();

        $results = $this->service->getAllLessons(['id', 'title']);

        $attributes = $results->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('order', $attributes);
    }

    public function test_get_lesson_returns_correct_lesson_by_id(): void
    {
        $lesson = Lesson::factory()->create();

        $result = $this->service->getLesson($lesson->id);

        $this->assertInstanceOf(Lesson::class, $result);
        $this->assertEquals($lesson->id, $result->id);
    }

    public function test_get_lesson_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getLesson(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_lesson_returns_only_selected_columns(): void
    {
        $lesson = Lesson::factory()->create();

        $result = $this->service->getLesson($lesson->id, ['id', 'title']);

        $this->assertInstanceOf(Lesson::class, $result);
        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('order', $attributes);
    }

    public function test_create_lesson_persists_and_returns_lesson(): void
    {
        $result = $this->service->createLesson([
            'title'       => 'Introduction',
            'description' => 'Intro lesson',
            'type'        => LessonTypeEnum::TEXT,
            'duration'    => 300,
            'order'       => 1,
            'content'     => 'Some content',
        ]);

        $this->assertInstanceOf(Lesson::class, $result);
        $this->assertDatabaseHas('lessons', ['title' => 'Introduction']);
    }

    public function test_create_lesson_with_module_reference_persists_module_id(): void
    {
        $module = Module::factory()->create();

        $result = $this->service->createLesson([
            'title'       => 'Getting Started',
            'description' => 'First lesson',
            'type'        => LessonTypeEnum::VIDEO,
            'duration'    => 420,
            'order'       => 1,
            'youtube_id'  => 'abc123',
            'module_id'   => $module->id,
        ]);

        $this->assertInstanceOf(Lesson::class, $result);
        $this->assertDatabaseHas('lessons', [
            'title'     => 'Getting Started',
            'module_id' => $module->id,
        ]);
    }

    public function test_update_lesson_modifies_attributes_and_returns_fresh_model(): void
    {
        $lesson = Lesson::factory()->create(['title' => 'Old Title']);

        $updated = $this->service->updateLesson($lesson->id, ['title' => 'New Title']);

        $this->assertInstanceOf(Lesson::class, $updated);
        $this->assertEquals('New Title', $updated->title);
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'New Title']);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent lesson' => ['updateLesson', [PHP_INT_MAX, ['title' => 'test']]],
            'delete nonexistent lesson' => ['deleteLesson', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_delete_lesson_removes_record_and_returns_true(): void
    {
        $lesson = Lesson::factory()->create();

        $result = $this->service->deleteLesson($lesson->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_update_order_reorders_lessons(): void
    {
        $lessonA = Lesson::factory()->create(['order' => 1]);
        $lessonB = Lesson::factory()->create(['order' => 2]);

        $result = $this->service->updateOrder([
            ['id' => $lessonA->id, 'order' => 2],
            ['id' => $lessonB->id, 'order' => 1],
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('lessons', ['id' => $lessonA->id, 'order' => 2]);
        $this->assertDatabaseHas('lessons', ['id' => $lessonB->id, 'order' => 1]);
    }
}
