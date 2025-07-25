<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\NewAccessToken;
use Mockery;

class AuthenticationServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticationService $service;
    private UserServiceInterface $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->service = new AuthenticationService($this->userService);
    }

    public function test_login_returns_user_and_token()
    {
        $hashedPassword = Hash::make('password123');
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn($hashedPassword);

        $token = Mockery::mock(NewAccessToken::class);
        $token->plainTextToken = 'fake-token';

        $this->userService
            ->expects('findByEmail')
            ->with('test@example.com')
            ->andReturn($user);

        $user->expects('createToken')
            ->with('auth-token')
            ->andReturn($token);

        $result = $this->service->login('test@example.com', 'password123');

        $this->assertEquals($user, $result['user']);
        $this->assertEquals('fake-token', $result['token']);
    }

    public function test_login_throws_exception_for_invalid_credentials()
    {
        $this->userService
            ->expects('findByEmail')
            ->with('test@example.com')
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->service->login('test@example.com', 'wrongpassword');
    }

    public function test_register_creates_student_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '123456789'
        ];

        $expectedData = array_merge($userData, ['role' => User::ROLE_STUDENT]);
        $user = new User($expectedData);

        $this->userService
            ->expects('createUser')
            ->with($expectedData)
            ->andReturn($user);

        $result = $this->service->register($userData);

        $this->assertEquals($user, $result);
    }

    public function test_logout_deletes_current_token()
    {
        $token = Mockery::mock();
        $token->expects('delete')->once();

        $user = Mockery::mock(User::class);
        $user->expects('currentAccessToken')
            ->andReturn($token);

        $this->service->logout($user);

        $this->assertTrue(true);
    }

    public function test_send_reset_password_email_success()
    {
        $user = new User(['email' => 'test@example.com']);

        $this->userService
            ->expects('findByEmail')
            ->with('test@example.com')
            ->andReturn($user);

        Password::shouldReceive('sendResetLink')
            ->with(['email' => 'test@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $this->service->sendResetPasswordEmail('test@example.com');

        $this->assertTrue(true); // No exception thrown
    }

    public function test_send_reset_password_email_throws_exception_for_invalid_user()
    {
        $this->userService
            ->expects('findByEmail')
            ->with('invalid@example.com')
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->service->sendResetPasswordEmail('invalid@example.com');
    }

    public function test_reset_password_success()
    {
        $resetData = [
            'token' => 'reset-token',
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        Password::shouldReceive('reset')
            ->with($resetData, Mockery::type('callable'))
            ->andReturn(Password::PASSWORD_RESET);

        $this->service->resetPassword($resetData);

        $this->assertTrue(true); // No exception thrown
    }

    public function test_reset_password_throws_exception_on_failure()
    {
        $resetData = [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        Password::shouldReceive('reset')
            ->andReturn(Password::INVALID_TOKEN);

        $this->expectException(\Exception::class);

        $this->service->resetPassword($resetData);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
