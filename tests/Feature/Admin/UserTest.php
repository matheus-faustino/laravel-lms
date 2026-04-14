<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('usersRoutes')]
    public function test_admin_can_access_users_routes(string $routeName, ?array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)->get(route($routeName, $params))->assertOk();
    }

    #[DataProvider('usersRoutes')]
    public function test_user_cant_access_users_routes(string $routeName, ?array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->get(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('usersRoutes')]
    public function test_unauthenticated_user_cant_access_users_routes(string $routeName, ?array $params = []): void
    {
        $this->get(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function usersRoutes(): array
    {
        return [
            'index' => ['admin.users.index'],
            'create' => ['admin.users.create'],
            'edit' => ['admin.users.edit', ['userId' => 1]],
        ];
    }

    #[DataProvider('mutationRoutes')]
    public function test_regular_user_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->{$method}(route($routeName, $params))->assertForbidden();
    }

    #[DataProvider('mutationRoutes')]
    public function test_unauthenticated_user_cant_access_mutation_routes(string $routeName, string $method, array $params = []): void
    {
        $this->{$method}(route($routeName, $params))->assertRedirect(route('login'));
    }

    public static function mutationRoutes(): array
    {
        return [
            'store'  => ['admin.users.store',  'post',   []],
            'update' => ['admin.users.update', 'put',    ['userId' => 999]],
            'delete' => ['admin.users.delete', 'delete', ['userId' => 999]],
        ];
    }

    public function test_index_return_only_paginated_users(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        User::factory(5)->create(['role' => RoleEnum::ADMIN]);
        User::factory(5)->create(['role' => RoleEnum::USER]);

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.user.index')
            ->assertViewHas('users', function (LengthAwarePaginator $users) {
                $items = $users->items();

                $this->assertCount(5, $items);
                $this->assertSame(['id', 'name', 'email', 'created_at', 'updated_at'], array_keys($items[0]->getAttributes()));

                return true;
            });
    }

    public function test_admin_can_create_user(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */

        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $userData = User::factory()->make(['role' => RoleEnum::USER])->toArray();

        $userData['password'] = 'password';
        $userData['password_confirmation'] = 'password';

        $this->actingAs($admin)->post(route('admin.users.create'), $userData)
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', __('pages.admin.users.created.success'));

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    #[DataProvider('invalidStorePayloads')]
    public function test_create_user_with_invalid_data_returns_validation_error(array $override, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $payload = array_merge([
            'name'                  => 'Valid Name',
            'email'                 => 'valid@email.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ], $override);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public function test_create_user_with_duplicate_email_returns_validation_error(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $existing = User::factory()->create(['role' => RoleEnum::USER]);

        $payload = [
            'name'                  => 'Another User',
            'email'                 => $existing->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $payload)
            ->assertSessionHasErrors(['email']);
    }

    public static function invalidStorePayloads(): array
    {
        return [
            'missing name'       => [['name' => ''],                                                     ['name']],
            'name too long'      => [['name' => str_repeat('a', 51)],                                    ['name']],
            'missing email'      => [['email' => ''],                                                    ['email']],
            'invalid email'      => [['email' => 'not-an-email'],                                        ['email']],
            'missing password'   => [['password' => '', 'password_confirmation' => ''],                  ['password']],
            'password too short' => [['password' => 'short1', 'password_confirmation' => 'short1'],      ['password']],
            'password mismatch'  => [['password_confirmation' => 'different'],                           ['password']],
        ];
    }

    public function test_admin_can_edit_user(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($admin)->get(route('admin.users.edit', ['userId' => $user->id]))
            ->assertOk()
            ->assertViewIs('admin.user.edit')
            ->assertViewHas('user', function (User $viewUser) use ($user) {
                return $viewUser->is($user);
            });
    }

    public function test_admin_can_update_user(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $userData = [
            'name'                  => 'Updated Name',
            'email'                 => 'updated@example.com',
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $this->actingAs($admin)
            ->put(route('admin.users.update', ['userId' => $user->id]), $userData)
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', __('pages.admin.users.updated.success'));

        $this->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    #[DataProvider('invalidUpdatePayloads')]
    public function test_update_user_with_invalid_data_returns_validation_error(array $override, array $expectedErrors): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $payload = array_merge([
            'name'                  => 'Valid Name',
            'email'                 => 'valid@email.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ], $override);

        $this->actingAs($admin)
            ->put(route('admin.users.update', ['userId' => $user->id]), $payload)
            ->assertSessionHasErrors($expectedErrors);
    }

    public function test_update_user_with_duplicate_email_of_another_user_returns_validation_error(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user = User::factory()->create(['role' => RoleEnum::USER]);
        $other = User::factory()->create(['role' => RoleEnum::USER]);

        $payload = [
            'name'                  => 'Updated Name',
            'email'                 => $other->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        $this->actingAs($admin)
            ->put(route('admin.users.update', ['userId' => $user->id]), $payload)
            ->assertSessionHasErrors(['email']);
    }

    public function test_update_user_keeping_own_email_succeeds(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $payload = [
            'name'                  => 'Updated Name',
            'email'                 => $user->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        $this->actingAs($admin)
            ->put(route('admin.users.update', ['userId' => $user->id]), $payload)
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_update_nonexistent_user_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $payload = [
            'name'                  => 'Valid Name',
            'email'                 => 'valid@email.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        $this->actingAs($admin)
            ->put(route('admin.users.update', ['userId' => 999]), $payload)
            ->assertNotFound();
    }

    public static function invalidUpdatePayloads(): array
    {
        return [
            'missing name'       => [['name' => ''],                                                     ['name']],
            'name too long'      => [['name' => str_repeat('a', 51)],                                    ['name']],
            'missing email'      => [['email' => ''],                                                    ['email']],
            'invalid email'      => [['email' => 'not-an-email'],                                        ['email']],
            'missing password'   => [['password' => '', 'password_confirmation' => ''],                  ['password']],
            'password too short' => [['password' => 'short1', 'password_confirmation' => 'short1'],      ['password']],
            'password mismatch'  => [['password_confirmation' => 'different'],                           ['password']],
        ];
    }

    public function test_admin_can_delete_user(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($admin)->delete(route('admin.users.delete', ['userId' => $user->id]))
            ->assertOk()
            ->assertJsonPath('message', __('pages.admin.users.deleted.success'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function test_delete_nonexistent_user_returns_not_found(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.users.delete', ['userId' => 999]))
            ->assertNotFound();
    }
}
