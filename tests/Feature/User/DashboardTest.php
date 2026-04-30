<?php

namespace Tests\Feature\User;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_dashboard(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $response = $this->actingAs($user)->get('/user/');

        $response->assertOk();
        $response->assertViewIs('user.dashboard.index');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/user/');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_user_dashboard(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $response = $this->actingAs($admin)->get('/user/');

        $response->assertForbidden();
    }
}
