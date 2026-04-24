<?php

namespace Tests\Unit;

use App\Enums\CourseStatusEnum;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseService $service;
    private FilesystemAdapter $disk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disk = Storage::fake('public');
        $this->service = new CourseService();
    }

    public function test_get_all_courses_returns_collection_of_all_courses(): void
    {
        Course::factory()->count(3)->create();

        $result = $this->service->getAllCourses();

        $this->assertCount(3, $result);
    }

    public function test_get_all_courses_returns_only_selected_columns(): void
    {
        Course::factory()->count(2)->create();

        $result = $this->service->getAllCourses(['id', 'title']);

        $attributes = $result->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('description', $attributes);
    }

    public function test_get_course_returns_correct_course_by_id(): void
    {
        $course = Course::factory()->create();

        $result = $this->service->getCourse($course->id);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertEquals($course->id, $result->id);
    }

    public function test_get_course_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getCourse(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_course_returns_only_selected_columns(): void
    {
        $course = Course::factory()->create();

        $result = $this->service->getCourse($course->id, ['id', 'title']);

        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('description', $attributes);
    }

    public function test_create_course_persists_and_returns_course(): void
    {
        $result = $this->service->createCourse([
            'title' => 'Laravel Course',
            'description' => 'A comprehensive Laravel course.',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
            'banner' => UploadedFile::fake()->image('banner.jpg'),
            'status' => CourseStatusEnum::DRAFT,
        ]);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertDatabaseHas('courses', ['title' => 'Laravel Course']);
    }

    public function test_create_course_stores_images_in_correct_directories(): void
    {
        $result = $this->service->createCourse([
            'title' => 'Test',
            'description' => 'Test description.',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
            'banner' => UploadedFile::fake()->image('banner.jpg'),
            'status' => CourseStatusEnum::DRAFT,
        ]);

        $this->assertStringStartsWith('courses/thumbnails/', $result->thumbnail);
        $this->assertStringStartsWith('courses/banners/', $result->banner);
        $this->disk->assertExists($result->thumbnail);
        $this->disk->assertExists($result->banner);
    }

    public function test_update_course_modifies_attributes_and_returns_fresh_model(): void
    {
        $course = Course::factory()->create(['title' => 'Old Title']);

        $updated = $this->service->updateCourse($course->id, ['title' => 'New Title']);

        $this->assertInstanceOf(Course::class, $updated);
        $this->assertEquals('New Title', $updated->title);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'New Title']);
    }

    public function test_update_course_replaces_thumbnail_and_deletes_old_one(): void
    {
        $course = $this->service->createCourse([
            'title' => 'Test',
            'description' => 'Test.',
            'thumbnail' => UploadedFile::fake()->image('old_thumbnail.jpg'),
            'banner' => UploadedFile::fake()->image('old_banner.jpg'),
            'status' => CourseStatusEnum::DRAFT,
        ]);

        $oldThumbnailPath = $course->thumbnail;

        $updated = $this->service->updateCourse($course->id, [
            'thumbnail' => UploadedFile::fake()->image('new_thumbnail.jpg'),
        ]);

        $this->disk->assertMissing($oldThumbnailPath);
        $this->disk->assertExists($updated->thumbnail);
        $this->assertNotEquals($oldThumbnailPath, $updated->thumbnail);
    }

    public function test_update_course_replaces_banner_and_deletes_old_one(): void
    {
        $course = $this->service->createCourse([
            'title' => 'Test',
            'description' => 'Test.',
            'thumbnail' => UploadedFile::fake()->image('old_thumbnail.jpg'),
            'banner' => UploadedFile::fake()->image('old_banner.jpg'),
            'status' => CourseStatusEnum::DRAFT,
        ]);

        $oldBannerPath = $course->banner;

        $updated = $this->service->updateCourse($course->id, [
            'banner' => UploadedFile::fake()->image('new_banner.jpg'),
        ]);

        $this->disk->assertMissing($oldBannerPath);
        $this->disk->assertExists($updated->banner);
        $this->assertNotEquals($oldBannerPath, $updated->banner);
    }

    public function test_update_course_does_not_change_images_when_not_provided(): void
    {
        $course = Course::factory()->create(['title' => 'Old Title']);

        $updated = $this->service->updateCourse($course->id, ['title' => 'New Title']);

        $this->assertEquals($course->thumbnail, $updated->thumbnail);
        $this->assertEquals($course->banner, $updated->banner);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent course' => ['updateCourse', [PHP_INT_MAX, ['title' => 'test']]],
            'delete nonexistent course' => ['deleteCourse', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_delete_course_removes_record_and_returns_true(): void
    {
        $course = Course::factory()->create();

        $result = $this->service->deleteCourse($course->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_delete_course_removes_images_from_storage(): void
    {
        $course = $this->service->createCourse([
            'title' => 'Test',
            'description' => 'Test.',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
            'banner' => UploadedFile::fake()->image('banner.jpg'),
            'status' => CourseStatusEnum::DRAFT,
        ]);

        $thumbnailPath = $course->thumbnail;
        $bannerPath = $course->banner;

        $this->service->deleteCourse($course->id);

        $this->disk->assertMissing($thumbnailPath);
        $this->disk->assertMissing($bannerPath);
    }

    public function test_get_paginated_courses_returns_paginator_instance(): void
    {
        Course::factory()->count(5)->create();

        $result = $this->service->getPaginatedCourses(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
    }

    public function test_get_paginated_courses_respects_per_page_limit(): void
    {
        Course::factory()->count(10)->create();

        $result = $this->service->getPaginatedCourses(3);

        $this->assertCount(3, $result->items());
        $this->assertEquals(10, $result->total());
    }

    public function test_get_paginated_courses_filters_by_criteria(): void
    {
        Course::factory()->count(3)->published()->create();
        Course::factory()->count(2)->draft()->create();

        $result = $this->service->getPaginatedCourses(10, ['status' => CourseStatusEnum::PUBLISHED->value]);

        $this->assertCount(3, $result->items());
    }

    public function test_get_paginated_courses_returns_only_selected_columns(): void
    {
        Course::factory()->count(3)->create();

        $result = $this->service->getPaginatedCourses(10, [], ['id', 'title']);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $attributes = $result->items()[0]->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayNotHasKey('description', $attributes);
    }
}
