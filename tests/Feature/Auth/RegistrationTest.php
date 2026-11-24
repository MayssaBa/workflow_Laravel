<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('Teamo')
            ->assertSee('Créez votre espace Teamo')
            ->assertSee('data-password-strength', false)
            ->assertSee('8 caractères minimum');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'employee',
            'is_active' => true,
        ]);
    }

    public function test_registration_rejects_unconfirmed_password_and_shows_field_error(): void
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $this->assertGuest();
        $response->assertOk()
            ->assertSee('id="password-error"', false)
            ->assertSee('La confirmation du champ mot de passe ne correspond pas.');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
