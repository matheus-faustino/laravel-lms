<?php

namespace Tests\Feature\Controllers\Api\Student;

use Tests\TestCase;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private UserServiceInterface $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->app->instance(UserServiceInterface::class, $this->userService);
    }

    public function test_show_returns_authenticated_user_profile()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/student/profile');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]);
    }

    public function test_update_updates_profile_successfully()
    {
        $user = User::factory()->student()->create();
        $updateData = [
            'name' => 'Updated Name',
            'phone' => '123456789'
        ];

        $updatedUser = User::factory()->make([
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '123456789'
        ]);

        Sanctum::actingAs($user);

        $this->userService
            ->expects('updateProfile')
            ->with($user->id, $updateData)
            ->andReturn(true);

        $this->userService
            ->expects('findOrFail')
            ->with($user->id)
            ->andReturn($updatedUser);

        $response = $this->putJson('/api/student/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Updated Name',
                    'phone' => '123456789'
                ]
            ]);
    }

    public function test_update_validates_input()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/student/profile', [
            'name' => str_repeat('a', 256), // Too long
            'phone' => str_repeat('1', 21)  // Too long
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    public function test_change_password_successfully()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $this->userService
            ->expects('changePassword')
            ->with($user->id, 'current_password', 'new_password123')
            ->andReturn(true);

        $response = $this->putJson('/api/student/change-password', [
            'current_password' => 'current_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password changed successfully'
            ]);
    }

    public function test_change_password_fails_with_wrong_current_password()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $this->userService
            ->expects('changePassword')
            ->with($user->id, 'wrong_password', 'new_password123')
            ->andThrow(new \Exception('Current password is incorrect'));

        $response = $this->putJson('/api/student/change-password', [
            'current_password' => 'wrong_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Current password is incorrect'
            ]);
    }

    public function test_change_password_validates_required_fields()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/student/change-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password', 'password']);
    }

    public function test_change_password_validates_password_confirmation()
    {
        $user = User::factory()->student()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/student/change-password', [
            'current_password' => 'current_password',
            'password' => 'new_password123',
            'password_confirmation' => 'different_password'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/student/profile');
        $response->assertStatus(401);

        $response = $this->putJson('/api/student/profile', []);
        $response->assertStatus(401);

        $response = $this->putJson('/api/student/change-password', []);
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
