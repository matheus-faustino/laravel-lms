<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryService();
    }

    public function test_get_all_categories_returns_collection_of_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $categories = $this->service->getAllCategories();

        $this->assertCount(3, $categories);
    }

    public function test_get_all_categories_returns_only_selected_columns(): void
    {
        Category::factory()->count(2)->create();

        $results = $this->service->getAllCategories(['id', 'name']);

        $attributes = $results->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('category_id', $attributes);
    }

    public function test_get_all_parent_categories_returns_only_root_categories(): void
    {
        $parent = Category::factory()->create();
        Category::factory()->count(3)->create(['category_id' => $parent->id]);

        $results = $this->service->getAllParentCategories();

        $this->assertCount(1, $results);
        $this->assertEquals($parent->id, $results->first()->id);
    }

    public function test_get_all_parent_categories_returns_empty_collection_when_none_exist(): void
    {
        $results = $this->service->getAllParentCategories();

        $this->assertCount(0, $results);
    }

    public function test_get_all_parent_categories_returns_only_selected_columns(): void
    {
        Category::factory()->count(2)->create();

        $results = $this->service->getAllParentCategories(['id', 'name']);

        $attributes = $results->first()->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('category_id', $attributes);
    }

    public function test_get_category_returns_correct_category_by_id(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->getCategory($category->id);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
    }

    public function test_get_category_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getCategory(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_category_returns_only_selected_columns(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->getCategory($category->id, ['id', 'name']);

        $this->assertInstanceOf(Category::class, $result);
        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('category_id', $attributes);
    }

    public function test_create_category_persists_and_returns_category(): void
    {
        $result = $this->service->createCategory(['name' => 'Electronics']);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_create_subcategory_persists_with_parent_reference(): void
    {
        $parent = Category::factory()->create();

        $result = $this->service->createCategory([
            'name' => 'Smartphones',
            'category_id' => $parent->id,
        ]);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertDatabaseHas('categories', [
            'name' => 'Smartphones',
            'category_id' => $parent->id,
        ]);
    }

    public function test_update_category_modifies_attributes_and_returns_fresh_model(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $updated = $this->service->updateCategory($category->id, ['name' => 'New Name']);

        $this->assertInstanceOf(Category::class, $updated);
        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent category' => ['updateCategory', [PHP_INT_MAX, ['name' => 'test']]],
            'delete nonexistent category' => ['deleteCategory', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_delete_category_removes_record_and_returns_true(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->deleteCategory($category->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_get_paginated_categories_returns_paginator_instance(): void
    {
        Category::factory()->count(5)->create();

        $result = $this->service->getPaginatedCategories(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(5, $result->items());
    }

    public function test_get_paginated_categories_respects_per_page_limit(): void
    {
        Category::factory()->count(10)->create();

        $result = $this->service->getPaginatedCategories(3);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(3, $result->items());
        $this->assertEquals(10, $result->total());
    }

    public function test_get_paginated_categories_filters_by_criteria(): void
    {
        $parent = Category::factory()->create();
        Category::factory()->count(4)->create(['category_id' => $parent->id]);
        Category::factory()->count(2)->create();

        $result = $this->service->getPaginatedCategories(10, ['category_id' => $parent->id]);

        $this->assertCount(4, $result->items());
    }

    public function test_get_paginated_categories_returns_only_selected_columns(): void
    {
        Category::factory()->count(3)->create();

        $columns = ['id', 'name'];
        $result = $this->service->getPaginatedCategories(10, [], $columns);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $attributes = $result->items()[0]->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('category_id', $attributes);
    }
}
