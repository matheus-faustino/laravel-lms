<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $response = $this->actingAs($admin)->get('/admin/');

        $response->assertOk();
        $response->assertViewIs('admin.dashboard.index');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/');

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get('/admin/');

        $response->assertForbidden();
    }

    public function test_dashboard_shows_correct_users_count(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        User::factory()->count(10)->create(['role' => RoleEnum::USER]);

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $response = $this->actingAs($admin)->get('/admin/');

        $response->assertOk();
        $response->assertViewHas('usersCount', 10);
    }

    public function test_admin_users_are_not_counted_in_dashboard(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        User::factory()->count(3)->create(['role' => RoleEnum::ADMIN]);
        User::factory()->count(5)->create(['role' => RoleEnum::USER]);

        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $response = $this->actingAs($admin)->get('/admin/');

        $response->assertViewHas('usersCount', 5);
    }
}
