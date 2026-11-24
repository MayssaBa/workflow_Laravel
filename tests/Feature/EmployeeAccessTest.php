<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EmployeeAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, bool}> */
    public static function roles(): array
    {
        return [
            'admin' => ['admin', true],
            'manager' => ['manager', false],
            'employee' => ['employee', false],
        ];
    }

    #[DataProvider('roles')]
    public function test_employee_list_and_creation_are_gated_by_role(string $role, bool $allowed): void
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);

        $index = $this->actingAs($user)->get(route('admin.employees.index'));
        $create = $this->get(route('admin.employees.create'));

        if ($allowed) {
            $index->assertOk();
            $create->assertOk();
        } else {
            $index->assertForbidden();
            $create->assertForbidden();
        }
    }

    #[DataProvider('roles')]
    public function test_employee_details_are_gated_by_role(string $role, bool $allowed): void
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('admin.employees.show', $employee));

        if ($allowed) {
            $response->assertOk()
                ->assertSee('Actif');
        } else {
            $response->assertForbidden();
        }
    }

    public function test_validation_errors_are_rendered_under_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->from(route('admin.employees.create'))
            ->followingRedirects()
            ->post(route('admin.employees.store'), [
                'name' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertOk()
            ->assertSee('id="name-error"', false)
            ->assertSee('id="email-error"', false)
            ->assertSee('id="password-error"', false)
            ->assertSee('is-invalid');
    }
}
