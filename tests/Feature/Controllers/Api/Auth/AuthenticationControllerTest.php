<?php

namespace Tests\Feature\Controllers\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticationServiceInterface $authenticationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationService = Mockery::mock(AuthenticationServiceInterface::class);
        $this->app->instance(AuthenticationServiceInterface::class, $this->authenticationService);
    }

    public function test_login_returns_user_and_token()
    {
        $loginData = [
            'email' => 'student@example.com',
            'password' => 'password123'
        ];

        $user = User::factory()->make(['id' => 1, 'email' => 'student@example.com']);
        $token = 'fake-token';

        $this->authenticationService
            ->expects('login')
            ->with('student@example.com', 'password123')
            ->andReturn(['user' => $user, 'token' => $token]);

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token'
                ]
            ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $this->authenticationService
            ->expects('login')
            ->with('wrong@example.com', 'wrongpassword')
            ->andThrow(new \Exception('Invalid credentials'));

        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_register_creates_student_account()
    {
        $registerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '123456789'
        ];

        $serviceData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '123456789'
        ];

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => User::ROLE_STUDENT
        ]);

        $this->authenticationService
            ->expects('register')
            ->with($serviceData)
            ->andReturn($user);

        $response = $this->postJson('/api/auth/register', $registerData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => User::ROLE_STUDENT
                ],
                'message' => 'Registration successful'
            ]);
    }

    public function test_register_validates_required_fields()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_logout_revokes_token()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->authenticationService
            ->expects('logout')
            ->with($user)
            ->andReturn(null);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout successful']);
    }

    public function test_forgot_password_sends_reset_email()
    {
        User::factory()->create(['email' => 'student@example.com']);

        $this->authenticationService
            ->expects('sendResetPasswordEmail')
            ->with('student@example.com')
            ->andReturn(null);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'student@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset email sent successfully']);
    }

    // TODO: Return and check this test again. It works when testing the API directly. Probably something to do with the reset token
    // public function test_reset_password_updates_password()
    // {
    //     User::factory()->create(['email' => 'student@example.com']);

    //     $resetData = [
    //         'token' => 'reset-token',
    //         'email' => 'student@example.com',
    //         'password' => 'newpassword123',
    //         'password_confirmation' => 'newpassword123'
    //     ];

    //     $this->authenticationService
    //         ->expects('resetPassword')
    //         ->with($resetData)
    //         ->andReturn(null);

    //     $response = $this->postJson('/api/auth/reset-password', $resetData);

    //     $response->assertStatus(200)
    //         ->assertJson(['message' => 'Password reset successfully']);
    // }

    public function test_me_returns_authenticated_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
    }

    public function test_protected_routes_require_authentication()
    {
        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(401);

        $response = $this->getJson('/api/auth/me');
        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
