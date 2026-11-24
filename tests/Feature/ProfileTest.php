<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function roles(): array
    {
        return [
            'admin' => ['admin'],
            'manager' => ['manager'],
            'employee' => ['employee'],
        ];
    }

    #[DataProvider('roles')]
    public function test_profile_page_is_available_to_every_active_role(string $role): void
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee(strtoupper($role))
            ->assertSee($user->email)
            ->assertSee('Informations personnelles')
            ->assertSee('Sécurité');
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '+216 12 345 678',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('+216 12 345 678', $user->phone);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_password_update_rejects_incorrect_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword1!',
                'password_confirmation' => 'NewPassword1!',
            ])
            ->assertOk()
            ->assertSee('Le mot de passe est incorrect.');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'password',
                'password' => 'NewPassword1!',
                'password_confirmation' => 'NewPassword1!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertTrue(Hash::check('NewPassword1!', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
    }

    public function test_role_and_status_cannot_be_changed_through_profile_update(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                'role' => 'admin',
                'is_active' => false,
                'team_id' => null,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('employee', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_valid_avatar_is_stored_on_the_public_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        $this->assertStringStartsWith('avatars/', $user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_non_image_avatar_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->create('notes.txt', 32, 'text/plain'),
            ])
            ->assertOk()
            ->assertSee('Le champ photo de profil doit être un fichier de type');

        $this->assertNull($user->refresh()->avatar_path);
    }

    public function test_oversized_avatar_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(2049),
            ])
            ->assertOk()
            ->assertSee('La taille du fichier photo de profil ne doit pas dépasser 2048 kilo-octets.');

        $this->assertNull($user->refresh()->avatar_path);
    }

    public function test_old_avatar_is_deleted_when_replaced(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('avatars/old.jpg', 'old-avatar');

        $user = User::factory()->create(['avatar_path' => 'avatars/old.jpg']);

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('new-avatar.jpg', 200, 200),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        Storage::disk('public')->assertMissing('avatars/old.jpg');
        Storage::disk('public')->assertExists($user->avatar_path);
        $this->assertNotSame('avatars/old.jpg', $user->avatar_path);
    }

    public function test_profile_photo_can_be_deleted(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('avatars/current.jpg', 'current-avatar');

        $user = User::factory()->create(['avatar_path' => 'avatars/current.jpg']);

        $this->actingAs($user)
            ->delete(route('profile.avatar.destroy'))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        Storage::disk('public')->assertMissing('avatars/current.jpg');
        $this->assertNull($user->refresh()->avatar_path);
    }

    public function test_account_tab_has_no_editable_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('Ces informations sont gérées par un administrateur.')
            ->assertSee('Non affecté')
            ->assertDontSee('name="role"', false)
            ->assertDontSee('name="department"', false)
            ->assertDontSee('name="is_active"', false);
    }

    public function test_user_cannot_update_another_users_profile(): void
    {
        $user = User::factory()->create(['name' => 'Owner']);
        $otherUser = User::factory()->create(['name' => 'Other Person', 'email' => 'other@example.com']);

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Owner Updated',
                'email' => $user->email,
                'id' => $otherUser->id,
                'user_id' => $otherUser->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame('Owner Updated', $user->fresh()->name);
        $this->assertSame('Other Person', $otherUser->fresh()->name);
        $this->assertSame('other@example.com', $otherUser->fresh()->email);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->delete('/profile', [
                'password' => 'wrong-password',
            ])
            ->assertOk()
            ->assertSee('data-teamo-has-errors="true"', false)
            ->assertSee('delete_password-error', false);

        $this->assertNotNull($user->fresh());
    }
}
