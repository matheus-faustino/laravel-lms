<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // Middleware
    // -------------------------------------------------------------------------

    public function test_register_form_is_accessible_to_guests(): void
    {
        $this->get('/register')->assertOk();
    }

    #[DataProvider('authenticated_register_routes')]
    public function test_auth_middleware_blocks_authenticated_users(string $method, string $uri): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->{$method}($uri)->assertRedirect();
    }

    public static function authenticated_register_routes(): array
    {
        return [
            'GET /register'  => ['get', '/register'],
            'POST /register' => ['post', '/register'],
        ];
    }

    // -------------------------------------------------------------------------
    // Successful registration
    // -------------------------------------------------------------------------

    public function test_registration_persists_user_and_stores_hashed_password(): void
    {
        // The registration has Bug #2 (wrong route name), so it throws an
        // exception after creating the user. We catch it to still assert DB state.
        try {
            $this->withoutExceptionHandling()
                ->post('/register', $this->validPayload());
        } catch (\Throwable) {
            // Expected: Bug #2 causes RouteNotFoundException after user creation.
        }

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertTrue(Hash::check('secret1234', $user->password));
        $this->assertNotEquals('secret1234', $user->password);
    }

    /**
     * The controller calls Auth::login($user) before the redirect, so even
     * though the redirect blows up due to Bug #2, the user is authenticated.
     *
     * @bug RegisterController redirects to route('welcome') but the route is
     *      named 'landing'. Fix: change redirect()->route('welcome') to
     *      redirect()->route('landing').
     */
    public function test_registration_logs_user_in_before_redirect_bug_occurs(): void
    {
        try {
            $this->withoutExceptionHandling()
                ->post('/register', $this->validPayload());
        } catch (\Throwable) {
            // Expected: RouteNotFoundException thrown after Auth::login().
        }

        $this->assertAuthenticated();
    }

    /**
     * @bug RegisterController::register() calls redirect()->route('welcome'),
     *      but no route named 'welcome' exists (it is named 'landing').
     *      The global exception handler returns 500 for this unhandled exception.
     *      Fix: change to redirect()->route('landing').
     */
    public function test_registration_fails_with_route_not_found_due_to_bug(): void
    {
        $this->post('/register', $this->validPayload())
            ->assertStatus(500);
    }

    // -------------------------------------------------------------------------
    // Validation — FormRequest level
    //
    // Uses withoutExceptionHandling() because bootstrap/app.php's \Throwable
    // renderable intercepts ValidationException before the default redirect-back
    // behavior can run (see Bug #4 in LoginTest).
    // -------------------------------------------------------------------------

    #[DataProvider('invalid_registration_payloads')]
    public function test_registration_validates_input(array $payload, string $field): void
    {
        $exception = null;

        try {
            $this->withoutExceptionHandling()->post('/register', $payload);
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception, "Expected a ValidationException for field: $field");
        $this->assertArrayHasKey($field, $exception->errors());
    }

    public static function invalid_registration_payloads(): array
    {
        $base = [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        return [
            'missing name'           => [array_merge($base, ['name' => '']),                                                              'name'],
            'missing email'          => [array_merge($base, ['email' => '']),                                                             'email'],
            'password too short'     => [array_merge($base, ['password' => 'short12', 'password_confirmation' => 'short12']),             'password'],
            'passwords do not match' => [array_merge($base, ['password_confirmation' => 'different']),                                    'password'],
        ];
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $exception = null;

        try {
            $this->withoutExceptionHandling()->post('/register', $this->validPayload());
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertNotNull($exception, 'Expected a ValidationException for duplicate email');
        $this->assertArrayHasKey('email', $exception->errors());
    }
}
