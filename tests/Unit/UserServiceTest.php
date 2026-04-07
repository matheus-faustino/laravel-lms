<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tests for UserService.
 *
 * Extends Tests\TestCase (not PHPUnit\Framework\TestCase) because UserService
 * depends on Eloquent and DB::transaction, which require a booted Laravel app.
 * RefreshDatabase provides an isolated SQLite database for each test.
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    #[DataProvider('get_user_scenarios')]
    public function test_get_user_returns_correct_user_or_null_by_id(string $scenario): void
    {
        $user = User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);

        match ($scenario) {
            'existing_id'     => $this->assertSame($user->id, $this->service->getUser($user->id)?->id),
            'nonexistent_id'  => $this->assertNull($this->service->getUser(99999)),
            'column_selection' => $this->assertNull($this->service->getUser($user->id, ['id', 'name'])?->getRawOriginal('email')),
        };
    }

    public static function get_user_scenarios(): array
    {
        return [
            'returns user for existing id'   => ['existing_id'],
            'returns null for nonexistent id' => ['nonexistent_id'],
            'omits columns not requested'    => ['column_selection'],
        ];
    }

    public function test_create_user_persists_and_returns_user(): void
    {
        $attributes = [
            'name'     => 'Bob',
            'email'    => 'bob@example.com',
            'password' => 'secret1234',
            'role' => RoleEnum::USER,
        ];

        $user = $this->service->createUser($attributes);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'bob@example.com']);
    }

    public function test_update_user_modifies_attributes_and_returns_fresh_model(): void
    {
        $user = User::factory()->create(['name' => 'Carol']);

        $updated = $this->service->updateUser($user->id, ['name' => 'Carol Updated']);

        $this->assertSame('Carol Updated', $updated->name);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Carol Updated']);
    }

    #[DataProvider('missing_id_operations')]
    public function test_throws_model_not_found_when_id_is_missing(string $operation): void
    {
        $this->expectException(ModelNotFoundException::class);

        match ($operation) {
            'update' => $this->service->updateUser(99999, ['name' => 'Ghost']),
            'delete' => $this->service->deleteUser(99999),
        };
    }

    public static function missing_id_operations(): array
    {
        return [
            'updateUser with nonexistent id' => ['update'],
            'deleteUser with nonexistent id' => ['delete'],
        ];
    }

    public function test_delete_user_removes_record_and_returns_true(): void
    {
        $user = User::factory()->create();

        $result = $this->service->deleteUser($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_get_users_count_returns_users_count(): void
    {
        User::factory(10)->create();

        $result = $this->service->getUsersCount();

        $this->assertEquals(10, $result);
    }
}
