<?php

// tests/Unit/Services/LessonServiceTest.php
namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Lesson;
use App\Services\LessonService;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class LessonServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonService $service;
    private LessonRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(LessonRepositoryInterface::class);
        $this->service = new LessonService($this->repository);
    }

    public function test_create_lesson_validates_video_content()
    {
        $data = [
            'module_id' => 1,
            'title' => 'Video Lesson',
            'description' => 'Test',
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://youtube.com/watch?v=123'
        ];

        $lesson = Lesson::factory()->make($data);

        $this->repository
            ->expects('getNextOrder')
            ->with(1)
            ->andReturn(1);

        $this->repository
            ->expects('create')
            ->with(array_merge($data, ['order' => 1, 'content' => null]))
            ->andReturn($lesson);

        $result = $this->service->createLesson($data);

        $this->assertEquals($lesson, $result);
    }

    public function test_create_lesson_validates_text_content()
    {
        $data = [
            'module_id' => 1,
            'title' => 'Text Lesson',
            'description' => 'Test',
            'type' => Lesson::TYPE_TEXT,
            'content' => 'Lesson content here'
        ];

        $lesson = Lesson::factory()->make($data);

        $this->repository
            ->expects('getNextOrder')
            ->with(1)
            ->andReturn(1);

        $this->repository
            ->expects('create')
            ->with(array_merge($data, ['order' => 1, 'video_url' => null]))
            ->andReturn($lesson);

        $result = $this->service->createLesson($data);

        $this->assertEquals($lesson, $result);
    }

    public function test_create_lesson_throws_exception_for_missing_video_url()
    {
        $data = [
            'module_id' => 1,
            'title' => 'Video Lesson',
            'type' => Lesson::TYPE_VIDEO
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Video URL is required for video lessons');

        $this->service->createLesson($data);
    }

    public function test_delete_lesson_reorders_others()
    {
        $lesson = Lesson::factory()->make(['id' => 1, 'module_id' => 2, 'order' => 3]);

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($lesson);

        $this->repository
            ->expects('delete')
            ->with(1)
            ->andReturn(true);

        $this->repository
            ->expects('reorderAfterDeletion')
            ->with(2, 3);

        $result = $this->service->deleteLesson(1);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
