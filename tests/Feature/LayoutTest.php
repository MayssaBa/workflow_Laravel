<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_sidebar_and_navlinks(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('admin-sidebar')
            ->assertSee('admin-nav-link');
    }

    public function test_manager_dashboard_has_sidebar_and_navlinks(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $this->actingAs($manager)->get('/manager/dashboard')
            ->assertOk()
            ->assertSee('admin-sidebar')
            ->assertSee('admin-nav-link')
            ->assertSee('href="'.route('projects.index').'" class="text-decoration-none text-reset d-block"', false)
            ->assertSee('href="'.route('tasks.index').'"', false);
    }

    public function test_employee_dashboard_has_sidebar_and_navlinks(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $this->actingAs($employee)->get('/employee/dashboard')
            ->assertOk()
            ->assertSee('admin-sidebar')
            ->assertSee('admin-nav-link')
            ->assertSee('href="'.route('projects.index').'" class="text-decoration-none text-reset d-block"', false)
            ->assertSee('href="'.route('tasks.index').'"', false);
    }

    public function test_admin_sidebar_unlocks_department_and_team_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('admin.departments.index').'"', false)
            ->assertSee('href="'.route('admin.teams.index').'"', false)
            ->assertDontSee('class="admin-nav-link disabled"', false);
    }

    public function test_admin_department_and_team_pages_render(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $department = Department::factory()->create(['name' => 'Ressources humaines']);
        $team = Team::create(['name' => 'Recrutement', 'department_id' => $department->id]);

        $this->actingAs($admin);

        $this->get(route('admin.departments.index'))->assertOk();
        $this->get(route('admin.departments.create'))->assertOk();
        $this->get(route('admin.departments.show', $department))->assertOk();
        $this->get(route('admin.departments.edit', $department))->assertOk();
        $this->get(route('admin.teams.index'))->assertOk();
        $this->get(route('admin.teams.show', $team))->assertOk();
        $this->get(route('admin.teams.edit', $team))->assertOk();
    }
}
