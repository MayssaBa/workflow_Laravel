<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Teamo')
            ->assertSee('Bon retour sur Teamo')
            ->assertSee('Créer un compte');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('employee.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertOk()
            ->assertSee('id="email-error"', false)
            ->assertSee('Ces identifiants ne correspondent pas à nos enregistrements.');
    }

    public function test_inactive_user_can_not_authenticate_and_sees_error(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->from('/login')->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertOk()
            ->assertSee('id="email-error"', false)
            ->assertSee('Ces identifiants ne correspondent pas à nos enregistrements.');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
