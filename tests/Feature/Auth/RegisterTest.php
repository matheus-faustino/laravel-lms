<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_form_is_accessible_to_guests(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertViewIs('auth.register');
    }

    public function test_authenticated_user_is_redirected_from_register_form(): void
    {
        $user = User::factory()->create();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect();
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'name' => 'John Doe']);
    }

    public function test_registered_user_has_user_role(): void
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => RoleEnum::USER->value,
        ]);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public static function registration_validation_provider(): array
    {
        return [
            'missing name' => [
                ['email' => 'john@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'],
                ['name'],
            ],
            'missing email' => [
                ['name' => 'John', 'password' => 'password123', 'password_confirmation' => 'password123'],
                ['email'],
            ],
            'password too short' => [
                ['name' => 'John', 'email' => 'john@example.com', 'password' => 'short', 'password_confirmation' => 'short'],
                ['password'],
            ],
            'password confirmation mismatch' => [
                ['name' => 'John', 'email' => 'john@example.com', 'password' => 'password123', 'password_confirmation' => 'different'],
                ['password'],
            ],
        ];
    }

    #[DataProvider('registration_validation_provider')]
    public function test_registration_validation_errors(array $data, array $expectedErrors): void
    {
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors($expectedErrors);
        $this->assertGuest();
    }
}
