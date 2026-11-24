<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
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

    #[DataProvider('roles')]
    public function test_login_redirects_user_to_role_dashboard(string $role, string $expectedRoute): void
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route($expectedRoute, absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_inactive_account(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'is_active' => false]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Ces identifiants ne correspondent pas à nos enregistrements.']);
        $this->assertGuest();
    }
}
