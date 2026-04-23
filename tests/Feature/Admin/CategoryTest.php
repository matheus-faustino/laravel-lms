<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('categoryGetRoutes')]
    public function test_admin_can_access_category_get_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)->get(route($routeName))->assertOk();
    }

    #[DataProvider('categoryGetRoutes')]
    public function test_user_cant_access_category_get_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->get(route($routeName))->assertForbidden();
    }

    #[DataProvider('categoryGetRoutes')]
    public function test_unauthenticated_cant_access_category_get_routes(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    public static function categoryGetRoutes(): array
    {
        return [
            'index'  => ['admin.categories.index'],
            'create' => ['admin.categories.create'],
        ];
    }

    public function test_admin_can_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $category = Category::factory()->create();

        $this->actingAs($admin)->get(route('admin.categories.edit', $category->id))->assertOk();
    }

    public function test_user_cant_access_edit_route(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $category = Category::factory()->create();

        $this->actingAs($user)->get(route('admin.categories.edit', $category->id))->assertForbidden();
    }

    public function test_unauthenticated_cant_access_edit_route(): void
    {
        $category = Category::factory()->create();

        $this->get(route('admin.categories.edit', $category->id))->assertRedirect(route('login'));
    }

    #[DataProvider('mutationRoutes')]
    public function test_user_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->{$method}(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('mutationRoutes')]
    public function test_unauthenticated_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        $this->{$method}(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function mutationRoutes(): array
    {
        return [
            'store'  => ['admin.categories.store',  'post',   []],
            'update' => ['admin.categories.update', 'put',    ['categoryId' => 999]],
            'delete' => ['admin.categories.delete', 'delete', ['categoryId' => 999]],
        ];
    }

    public function test_index_returns_paginated_categories(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        Category::factory(15)->create();

        $this->actingAs($admin)
            ->get(route('admin.categories.index', ['perPage' => 10]))
            ->assertOk()
            ->assertViewIs('admin.category.index')
            ->assertViewHas('categories', function (LengthAwarePaginator $categories) {
                $items = $categories->items();

                $this->assertCount(10, $items);
                $this->assertSame(15, $categories->total());
                $this->assertSame(['id', 'name', 'category_id', 'created_at'], array_keys($items[0]->getAttributes()));

                return true;
            });
    }

    public function test_create_view_passes_only_root_categories_as_parent_options(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $rootCategory = Category::factory()->create();
        Category::factory()->create(['category_id' => $rootCategory->id]);

        $this->actingAs($admin)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertViewIs('admin.category.create')
            ->assertViewHas('parentOptions', function (Collection $options) use ($rootCategory) {
                return $options->count() === 1
                    && $options->first()->id === $rootCategory->id;
            });
    }

    public function test_admin_can_create_root_category(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'Electronics', 'category_id' => null])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', __('admin/categories.created_success'));

        $this->assertDatabaseHas('categories', ['name' => 'Electronics', 'category_id' => null]);
    }

    public function test_admin_can_create_subcategory(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $parent = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'Phones', 'category_id' => $parent->id])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', __('admin/categories.created_success'));

        $this->assertDatabaseHas('categories', ['name' => 'Phones', 'category_id' => $parent->id]);
    }

    public function test_create_category_with_duplicate_name_returns_validation_error(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        Category::factory()->create(['name' => 'Duplicate']);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'Duplicate'])
            ->assertSessionHasErrors(['name']);
    }

    #[DataProvider('invalidStorePayloads')]
    public function test_create_category_with_invalid_data_returns_validation_error(array $payload, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidStorePayloads(): array
    {
        return [
            'missing name'   => [['name' => ''],                                   ['name']],
            'name too long'  => [['name' => str_repeat('a', 51)],                  ['name']],
            'invalid parent' => [['name' => 'Valid', 'category_id' => 999999],     ['category_id']],
        ];
    }

    public function test_admin_can_access_edit_view_with_correct_data(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $parent = Category::factory()->create();
        $category = Category::factory()->create(['category_id' => $parent->id]);

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $category->id))
            ->assertOk()
            ->assertViewIs('admin.category.edit')
            ->assertViewHas('category', fn(Category $c) => $c->is($category))
            ->assertViewHas('parentOptions', function (Collection $options) use ($category) {
                return $options->doesntContain('id', $category->id);
            });
    }

    public function test_admin_can_update_category(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category->id), ['name' => 'New Name', 'category_id' => null])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', __('admin/categories.updated_success'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
        $this->assertDatabaseMissing('categories', ['id' => $category->id, 'name' => 'Old Name']);
    }

    public function test_update_category_keeping_own_name_succeeds(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $category = Category::factory()->create(['name' => 'Electronics']);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category->id), ['name' => 'Electronics', 'category_id' => null])
            ->assertRedirect(route('admin.categories.index'));
    }

    public function test_update_category_with_duplicate_name_returns_validation_error(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        Category::factory()->create(['name' => 'Taken']);
        $category = Category::factory()->create(['name' => 'Other']);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category->id), ['name' => 'Taken', 'category_id' => null])
            ->assertSessionHasErrors(['name']);
    }

    #[DataProvider('invalidUpdatePayloads')]
    public function test_update_category_with_invalid_data_returns_validation_error(array $override, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $category = Category::factory()->create();

        $payload = array_merge(['name' => 'Valid Name', 'category_id' => null], $override);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category->id), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public static function invalidUpdatePayloads(): array
    {
        return [
            'missing name'   => [['name' => ''],               ['name']],
            'name too long'  => [['name' => str_repeat('a', 51)], ['name']],
            'invalid parent' => [['category_id' => 999999],    ['category_id']],
        ];
    }

    public function test_update_nonexistent_category_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', ['categoryId' => 999]), ['name' => 'Valid', 'category_id' => null])
            ->assertNotFound();
    }

    public function test_admin_can_delete_category(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.categories.delete', $category->id))
            ->assertOk()
            ->assertJsonPath('message', __('admin/categories.deleted_success'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_nonexistent_category_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.delete', ['categoryId' => 999]))
            ->assertNotFound();
    }
}
