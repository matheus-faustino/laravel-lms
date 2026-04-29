<?php

namespace Tests\Unit;

use App\Enums\EnrollmentStatusEnum;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnrollmentService();
    }

    public function test_get_all_enrollments_returns_collection_of_all_enrollments(): void
    {
        Enrollment::factory()->count(3)->create();

        $result = $this->service->getAllEnrollments();

        $this->assertCount(3, $result);
    }

    public function test_get_all_enrollments_returns_only_selected_columns(): void
    {
        Enrollment::factory()->count(2)->create();

        $result = $this->service->getAllEnrollments(['id', 'status']);

        $attributes = $result->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('status', $attributes);
        $this->assertArrayNotHasKey('user_id', $attributes);
    }

    public function test_get_enrollment_returns_correct_enrollment_by_id(): void
    {
        $enrollment = Enrollment::factory()->create();

        $result = $this->service->getEnrollment($enrollment->id);

        $this->assertInstanceOf(Enrollment::class, $result);
        $this->assertEquals($enrollment->id, $result->id);
    }

    public function test_get_enrollment_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getEnrollment(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_enrollment_returns_only_selected_columns(): void
    {
        $enrollment = Enrollment::factory()->create();

        $result = $this->service->getEnrollment($enrollment->id, ['id', 'status']);

        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('status', $attributes);
        $this->assertArrayNotHasKey('user_id', $attributes);
    }

    public function test_create_enrollment_persists_and_returns_enrollment(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();

        $result = $this->service->createEnrollment([
            'status' => EnrollmentStatusEnum::ACTIVE,
            'course_id' => $course->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Enrollment::class, $result);
        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_update_enrollment_modifies_status_and_returns_fresh_model(): void
    {
        $enrollment = Enrollment::factory()->active()->create();

        $updated = $this->service->updateEnrollment($enrollment->id, [
            'status' => EnrollmentStatusEnum::CANCELLED,
        ]);

        $this->assertInstanceOf(Enrollment::class, $updated);
        $this->assertEquals(EnrollmentStatusEnum::CANCELLED, $updated->status);
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => EnrollmentStatusEnum::CANCELLED->value,
        ]);
    }

    public function test_delete_enrollment_removes_record_and_returns_true(): void
    {
        $enrollment = Enrollment::factory()->create();

        $result = $this->service->deleteEnrollment($enrollment->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent enrollment' => ['updateEnrollment', [PHP_INT_MAX, ['status' => 'cancelled']]],
            'delete nonexistent enrollment' => ['deleteEnrollment', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_get_paginated_enrollments_returns_paginator_instance(): void
    {
        Enrollment::factory()->count(5)->create();

        $result = $this->service->getPaginatedEnrollments(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
    }

    public function test_get_paginated_enrollments_respects_per_page_limit(): void
    {
        Enrollment::factory()->count(10)->create();

        $result = $this->service->getPaginatedEnrollments(3);

        $this->assertCount(3, $result->items());
        $this->assertEquals(10, $result->total());
    }

    public function test_get_paginated_enrollments_filters_by_criteria(): void
    {
        Enrollment::factory()->count(3)->active()->create();
        Enrollment::factory()->count(2)->cancelled()->create();

        $result = $this->service->getPaginatedEnrollments(10, ['status' => EnrollmentStatusEnum::ACTIVE->value]);

        $this->assertCount(3, $result->items());
    }

    public function test_get_paginated_enrollments_returns_only_selected_columns(): void
    {
        Enrollment::factory()->count(3)->create();

        $result = $this->service->getPaginatedEnrollments(10, [], ['id', 'status']);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $attributes = $result->items()[0]->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('status', $attributes);
        $this->assertArrayNotHasKey('user_id', $attributes);
    }
}
