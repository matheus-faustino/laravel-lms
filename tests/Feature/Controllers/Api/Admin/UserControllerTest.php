<?php

namespace Tests\Feature\Controllers\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Sanctum\Sanctum;
use Mockery;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private UserServiceInterface $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->app->instance(UserServiceInterface::class, $this->userService);
    }

    public function test_index_returns_paginated_users()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $users = new LengthAwarePaginator(
            [User::factory()->make(['id' => 1])],
            1,
            15,
            1
        );

        $this->userService
            ->expects('searchUsers')
            ->with(['search' => 'test'], 15)
            ->andReturn($users);

        $response = $this->getJson('/api/admin/users?search=test&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'email', 'role']
                    ]
                ]
            ]);
    }

    public function test_store_creates_user_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '123456789',
            'role' => 'student'
        ];

        $user = User::factory()->make($userData);
        $user->id = 1;

        $this->userService
            ->expects('createUser')
            ->with($userData)
            ->andReturn($user);

        $response = $this->postJson('/api/admin/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_store_validates_unique_email()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'student'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_requires_admin_role()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'student'
        ]);

        $response->assertStatus(403);
    }

    public function test_show_returns_user()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $user = User::factory()->make(['id' => 1]);

        $this->userService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($user);

        $response = $this->getJson('/api/admin/users/1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1
                ]
            ]);
    }

    public function test_update_updates_user_successfully()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '987654321'
        ];

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Updated Name',
            'phone' => '987654321'
        ]);

        $this->userService
            ->expects('update')
            ->with(1, $updateData)
            ->andReturn(true);

        $this->userService
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($user);

        $response = $this->putJson('/api/admin/users/1', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'Updated Name'
                ]
            ]);
    }

    public function test_update_validates_unique_email()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        User::factory()->create(['id' => 2, 'email' => 'existing@example.com']);

        $response = $this->putJson('/api/admin/users/1', [
            'email' => 'existing@example.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_requires_admin_role()
    {
        $student = User::factory()->student()->create();
        Sanctum::actingAs($student);

        $response = $this->putJson('/api/admin/users/1', ['name' => 'New Name']);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_user()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->userService
            ->expects('delete')
            ->with(1)
            ->andReturn(true);

        $response = $this->deleteJson('/api/admin/users/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User deleted successfully'
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
