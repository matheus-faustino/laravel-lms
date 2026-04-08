<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_form_is_accessible(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_reset_link_is_sent_to_existing_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->post('/forgot-password', ['email' => 'user@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_send_reset_link_fails_for_unknown_email(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', ['email' => 'unknown@example.com']);

        $response->assertSessionHasErrors(['email']);
        Notification::assertNothingSent();
    }

    public static function forgot_password_validation_provider(): array
    {
        return [
            'missing email'        => [[], ['email']],
            'invalid email format' => [['email' => 'not-an-email'], ['email']],
        ];
    }

    #[DataProvider('forgot_password_validation_provider')]
    public function test_forgot_password_validation_errors(array $data, array $expectedErrors): void
    {
        $response = $this->post('/forgot-password', $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    public function test_reset_password_form_is_accessible(): void
    {
        $response = $this->get('/reset-password/some-token');

        $response->assertOk();
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', 'some-token');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Event::fake([PasswordReset::class]);
        $user = User::factory()->create(['email' => 'user@example.com']);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => 'user@example.com',
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        Event::assertDispatched(PasswordReset::class);
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->post('/reset-password', [
            'token'                 => 'invalid-token',
            'email'                 => 'user@example.com',
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public static function reset_password_validation_provider(): array
    {
        return [
            'missing token' => [
                ['email' => 'user@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'],
                ['token'],
            ],
            'missing email' => [
                ['token' => 'abc', 'password' => 'password123', 'password_confirmation' => 'password123'],
                ['email'],
            ],
            'invalid email format' => [
                ['token' => 'abc', 'email' => 'not-an-email', 'password' => 'password123', 'password_confirmation' => 'password123'],
                ['email'],
            ],
            'password too short' => [
                ['token' => 'abc', 'email' => 'user@example.com', 'password' => 'short', 'password_confirmation' => 'short'],
                ['password'],
            ],
            'password confirmation mismatch' => [
                ['token' => 'abc', 'email' => 'user@example.com', 'password' => 'password123', 'password_confirmation' => 'different'],
                ['password'],
            ],
        ];
    }

    #[DataProvider('reset_password_validation_provider')]
    public function test_reset_password_validation_errors(array $data, array $expectedErrors): void
    {
        $response = $this->post('/reset-password', $data);

        $response->assertSessionHasErrors($expectedErrors);
    }
}
