<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_get_all_users_returns_collection_of_all_users(): void
    {
        User::factory()->count(3)->create();

        $users = $this->service->getAllUsers();

        $this->assertCount(3, $users);
    }

    public function test_get_user_returns_correct_user_by_id(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getUser($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_get_user_returns_null_for_nonexistent_id(): void
    {
        $result = $this->service->getUser(PHP_INT_MAX);

        $this->assertNull($result);
    }

    public function test_get_user_returns_only_selected_columns(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getUser($user->id, ['id', 'name']);

        $this->assertInstanceOf(User::class, $result);
        $attributes = $result->getAttributes();
        $this->assertArrayHasKey('id', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('email', $attributes);
    }

    public function test_create_user_persists_and_returns_user(): void
    {
        $result = $this->service->createUser([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => RoleEnum::USER,
        ]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'name' => 'John Doe']);
    }

    public function test_update_user_modifies_attributes_and_returns_fresh_model(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $updated = $this->service->updateUser($user->id, ['name' => 'New Name']);

        $this->assertInstanceOf(User::class, $updated);
        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public static function model_not_found_operations_provider(): array
    {
        return [
            'update nonexistent user' => ['updateUser', [PHP_INT_MAX, ['name' => 'test']]],
            'delete nonexistent user' => ['deleteUser', [PHP_INT_MAX]],
        ];
    }

    #[DataProvider('model_not_found_operations_provider')]
    public function test_throws_model_not_found_when_id_is_missing(string $method, array $args): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->$method(...$args);
    }

    public function test_delete_user_removes_record_and_returns_true(): void
    {
        $user = User::factory()->create();

        $result = $this->service->deleteUser($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_get_users_count_returns_total_count(): void
    {
        User::factory()->count(10)->create(['role' => RoleEnum::USER]);

        $count = $this->service->getUsersCount(['role' => RoleEnum::USER]);

        $this->assertEquals(10, $count);
    }

    public function test_get_users_count_returns_filtered_count_by_role(): void
    {
        User::factory()->count(5)->create(['role' => RoleEnum::USER]);
        User::factory()->count(3)->create(['role' => RoleEnum::ADMIN]);

        $userCount = $this->service->getUsersCount(['role' => RoleEnum::USER]);
        $adminCount = $this->service->getUsersCount(['role' => RoleEnum::ADMIN]);

        $this->assertEquals(5, $userCount);
        $this->assertEquals(3, $adminCount);
    }
}
