<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // -------------------------------------------------------------------------
    // Forgot password form
    // -------------------------------------------------------------------------

    public function test_forgot_password_form_is_accessible(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    // -------------------------------------------------------------------------
    // Send reset link — happy path and controller-level failure
    // -------------------------------------------------------------------------

    public function test_reset_link_is_sent_to_registered_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /**
     * When the email is valid but not in the database, Password::sendResetLink()
     * returns INVALID_USER. The controller sets a session error via back()->withErrors()
     * (controller-level, not FormRequest-level — so session errors work correctly).
     */
    public function test_forgot_password_returns_error_for_unknown_email(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'ghost@example.com'])
            ->assertSessionHasErrors(['email']);

        Notification::assertNothingSent();
    }

    // -------------------------------------------------------------------------
    // Send reset link — FormRequest validation
    //
    // Uses withoutExceptionHandling() because the \Throwable renderable in
    // bootstrap/app.php intercepts ValidationException before the default
    // redirect-back handler runs (see Bug #4 in LoginTest).
    // -------------------------------------------------------------------------

    #[DataProvider('invalid_email_payloads_for_forgot_password')]
    public function test_forgot_password_validates_email_format(array $payload): void
    {
        Notification::fake();

        $exception = null;

        try {
            $this->withoutExceptionHandling()->post('/forgot-password', $payload);
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception, 'Expected a ValidationException');
        $this->assertArrayHasKey('email', $exception->errors());
        Notification::assertNothingSent();
    }

    public static function invalid_email_payloads_for_forgot_password(): array
    {
        return [
            'empty payload'        => [[]],
            'invalid email format' => [['email' => 'not-an-email']],
        ];
    }

    // -------------------------------------------------------------------------
    // Reset password form
    // -------------------------------------------------------------------------

    /**
     * @bug ResetPasswordController::resetPasswordForm(string $token) declares a
     *      $token parameter, but the route GET /reset-password has no {token}
     *      segment — Laravel cannot resolve the argument and throws a fatal error
     *      (500). Fix: change the route to GET /reset-password/{token}.
     */
    public function test_reset_password_form_returns_error_due_to_missing_token_route_param(): void
    {
        $this->get('/reset-password')->assertStatus(500);
    }

    // -------------------------------------------------------------------------
    // Successful password reset
    // -------------------------------------------------------------------------

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirectToRoute('login')
          ->assertSessionHas('status');
    }

    public function test_successful_reset_updates_password_and_invalidates_old_one(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $fresh = $user->fresh();
        $this->assertTrue(Hash::check('newpassword123', $fresh->password));
        $this->assertFalse(Hash::check('password', $fresh->password));
    }

    public function test_successful_reset_fires_event_and_consumes_token(): void
    {
        Event::fake([PasswordReset::class]);

        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        Event::assertDispatched(PasswordReset::class);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    // -------------------------------------------------------------------------
    // Reset password — controller-level failures
    //
    // These pass FormRequest validation but fail inside Password::reset().
    // The controller calls back()->withErrors() manually, so session errors work.
    // -------------------------------------------------------------------------

    #[DataProvider('controller_level_reset_failures')]
    public function test_reset_password_returns_error_for_invalid_attempts(array $overrides, string $field): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $payload = array_merge([
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], $overrides);

        $this->post('/reset-password', $payload)
            ->assertSessionHasErrors([$field]);
    }

    public static function controller_level_reset_failures(): array
    {
        return [
            'invalid token'  => [['token' => 'invalid-token'],          'email'],
            'unknown email'  => [['email' => 'ghost@example.com'],       'email'],
        ];
    }

    // -------------------------------------------------------------------------
    // Reset password — FormRequest validation
    //
    // Uses withoutExceptionHandling() for the same reason as other validation
    // tests (bootstrap/app.php \Throwable renderable — see Bug #4 in LoginTest).
    // -------------------------------------------------------------------------

    #[DataProvider('form_request_reset_invalid_cases')]
    public function test_reset_password_form_request_validates_input(array $overrides, string $field): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $payload = array_merge([
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], $overrides);

        $exception = null;

        try {
            $this->withoutExceptionHandling()->post('/reset-password', $payload);
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception, "Expected a ValidationException for field: $field");
        $this->assertArrayHasKey($field, $exception->errors());
    }

    public static function form_request_reset_invalid_cases(): array
    {
        return [
            'missing token field'    => [['token' => ''],                                                                 'token'],
            'passwords do not match' => [['password_confirmation' => 'different'],                                        'password'],
            'password too short'     => [['password' => 'short12', 'password_confirmation' => 'short12'],                 'password'],
        ];
    }
}
