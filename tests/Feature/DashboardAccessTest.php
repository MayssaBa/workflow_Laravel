<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string}> */
    public static function roles(): array
    {
        return [
            'admin' => ['admin', 'admin.dashboard'],
            'manager' => ['manager', 'manager.dashboard'],
            'employee' => ['employee', 'employee.dashboard'],
        ];
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    #[DataProvider('roles')]
    public function test_dashboard_redirects_active_user_to_role_dashboard(string $role, string $expectedRoute): void
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route($expectedRoute, absolute: false));

        $this->get(route($expectedRoute))->assertOk();
    }

    #[DataProvider('roles')]
    public function test_dashboard_displays_user_identity_and_logout_works(string $role, string $expectedRoute): void
    {
        $user = User::factory()->create([
            'name' => 'Utilisateur '.ucfirst($role),
            'role' => $role,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route($expectedRoute, absolute: false));

        $this->get(route($expectedRoute))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee(strtoupper($role))
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Déconnexion');

        $this->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out_and_redirected_to_login(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'is_active' => false]);

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
