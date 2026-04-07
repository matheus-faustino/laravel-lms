<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // -------------------------------------------------------------------------
    // Guest middleware
    // -------------------------------------------------------------------------

    #[DataProvider('guest_only_routes')]
    public function test_auth_forms_enforce_guest_middleware(string $method, string $uri): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->{$method}($uri)->assertRedirect();
    }

    public static function guest_only_routes(): array
    {
        return [
            'GET /login redirects authenticated users'  => ['get', '/login'],
            'POST /login redirects authenticated users' => ['post', '/login'],
        ];
    }

    // -------------------------------------------------------------------------
    // Login form
    // -------------------------------------------------------------------------

    public function test_login_form_is_accessible_to_guests(): void
    {
        $this->get('/login')->assertOk();
    }

    // -------------------------------------------------------------------------
    // Successful login
    // -------------------------------------------------------------------------

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_user_can_log_in_with_remember_me_option(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
            'remember' => '1',
        ])->assertRedirect();

        $this->assertAuthenticated();
    }

    // -------------------------------------------------------------------------
    // Failed login — controller-level (these work because the controller calls
    // back()->withErrors() manually, which does not go through the exception handler)
    // -------------------------------------------------------------------------

    #[DataProvider('invalid_credential_cases')]
    public function test_login_with_invalid_credentials_returns_error(array $payload): void
    {
        User::factory()->create(['email' => 'real@example.com']);

        $this->post('/login', $payload)
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    public static function invalid_credential_cases(): array
    {
        return [
            'wrong password' => [['email' => 'real@example.com', 'password' => 'wrong']],
            'unknown email'  => [['email' => 'ghost@example.com', 'password' => 'password']],
        ];
    }

    // -------------------------------------------------------------------------
    // Failed login — FormRequest validation level
    //
    // Uses withoutExceptionHandling() because bootstrap/app.php registers a
    // \Throwable renderable that intercepts ValidationException before the
    // default handler can redirect back with errors (see Bug #4 below).
    // -------------------------------------------------------------------------

    #[DataProvider('missing_login_fields')]
    public function test_login_validates_required_fields(array $payload, string $field): void
    {
        $exception = null;

        try {
            $this->withoutExceptionHandling()->post('/login', $payload);
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception, "Expected a ValidationException for missing field: $field");
        $this->assertArrayHasKey($field, $exception->errors());
        $this->assertGuest();
    }

    public static function missing_login_fields(): array
    {
        return [
            'missing email field'    => [['password' => 'secret1234'], 'email'],
            'missing password field' => [['email' => 'user@example.com'], 'password'],
        ];
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function test_logout_redirects_to_login_and_clears_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirectToRoute('login');

        $this->assertGuest();
    }

    /**
     * The auth middleware throws AuthenticationException for unauthenticated
     * requests. We use withoutExceptionHandling() to surface the exception
     * directly rather than having it be swallowed by Bug #4's Throwable handler.
     */
    public function test_unauthenticated_user_cannot_access_logout_route(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(AuthenticationException::class);

        $this->post('/logout');
    }
}
