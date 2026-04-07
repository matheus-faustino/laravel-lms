<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['admin.dashboard.index'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_admin_can_access_admin_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        $this->actingAs($admin)->get(route($routeName))
            ->assertOk();
    }

    #[DataProvider('adminRoutes')]
    public function test_user_cant_access_admin_routes(string $routeName): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => RoleEnum::USER]);

        $this->actingAs($user)->get(route($routeName))
            ->assertOk();
    }

    public function test_dashboard_index_can_be_accessed(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);

        User::factory(10)->create(['role' => RoleEnum::USER]);

        $this->actingAs($admin)->get(route('admin.dashboard.index'))
            ->assertOk()
            ->assertViewHas('usersCount', 10);
    }
}
