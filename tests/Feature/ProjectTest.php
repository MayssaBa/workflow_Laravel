<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, bool}> */
    public static function invalidMembers(): array
    {
        return [
            'manager' => ['manager', true],
            'admin' => ['admin', true],
            'inactive employee' => ['employee', false],
        ];
    }

    public function test_project_policy_matches_the_required_matrix(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $otherManager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $outsider = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Project::class));
        $this->assertTrue(Gate::forUser($manager)->allows('viewAny', Project::class));
        $this->assertTrue(Gate::forUser($employee)->allows('viewAny', Project::class));

        $this->assertTrue(Gate::forUser($admin)->allows('view', $project));
        $this->assertTrue(Gate::forUser($manager)->allows('view', $project));
        $this->assertTrue(Gate::forUser($employee)->allows('view', $project));
        $this->assertTrue(Gate::forUser($outsider)->denies('view', $project));
        $this->assertTrue(Gate::forUser($otherManager)->denies('view', $project));

        $this->assertTrue(Gate::forUser($manager)->allows('create', Project::class));
        $this->assertTrue(Gate::forUser($admin)->denies('create', Project::class));
        $this->assertTrue(Gate::forUser($employee)->denies('create', Project::class));

        $this->assertTrue(Gate::forUser($manager)->allows('update', $project));
        $this->assertTrue(Gate::forUser($manager)->allows('delete', $project));
        $this->assertTrue(Gate::forUser($manager)->allows('manageMembers', $project));
        $this->assertTrue(Gate::forUser($admin)->denies('update', $project));
        $this->assertTrue(Gate::forUser($admin)->denies('delete', $project));
        $this->assertTrue(Gate::forUser($admin)->denies('manageMembers', $project));
        $this->assertTrue(Gate::forUser($otherManager)->denies('update', $project));
        $this->assertTrue(Gate::forUser($employee)->denies('manageMembers', $project));
    }

    public function test_manager_creates_project_with_authenticated_manager(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($manager)
            ->post(route('projects.store'), [
                'name' => 'Projet Alpha',
                'description' => 'Description du projet',
                'start_date' => '2026-01-10',
                'end_date' => '2026-02-10',
                'manager_id' => User::factory()->create(['role' => 'manager'])->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirectContains('/projects/');

        $project = Project::where('name', 'Projet Alpha')->firstOrFail();

        $this->assertSame($manager->id, $project->manager_id);
        $this->assertSame('2026-01-10', $project->start_date->toDateString());
        $this->assertSame('2026-02-10', $project->end_date->toDateString());
    }

    public function test_manager_updates_own_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create(['name' => 'Ancien nom']);

        $this->actingAs($manager)
            ->put(route('projects.update', $project), [
                'name' => 'Nouveau nom',
                'description' => 'Nouvelle description',
                'start_date' => '2026-03-01',
                'end_date' => '2026-04-01',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('projects.show', $project));

        $project->refresh();

        $this->assertSame('Nouveau nom', $project->name);
        $this->assertSame('Nouvelle description', $project->description);
        $this->assertSame('2026-03-01', $project->start_date->toDateString());
        $this->assertSame('2026-04-01', $project->end_date->toDateString());
    }

    public function test_manager_soft_deletes_own_project_and_hides_it_from_list(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create(['name' => 'Projet masqué']);

        $this->actingAs($manager)
            ->delete(route('projects.destroy', $project))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('projects.index'));

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        $this->assertSame(1, Project::withTrashed()->whereKey($project->id)->count());

        $this->actingAs($manager)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertDontSee('Projet masqué');
    }

    public function test_manager_cannot_view_or_update_another_managers_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $otherManager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $project = Project::factory()->for($otherManager, 'manager')->create(['name' => 'Projet privé']);

        $this->actingAs($manager)
            ->get(route('projects.show', $project))
            ->assertForbidden();

        $this->get(route('projects.edit', $project))
            ->assertForbidden();

        $this->put(route('projects.update', $project), ['name' => 'Nom modifié'])
            ->assertForbidden();

        $this->assertSame('Projet privé', $project->fresh()->name);
    }

    public function test_employee_is_refused_project_management_actions(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->actingAs($employee)
            ->get(route('projects.create'))
            ->assertForbidden();

        $this->post(route('projects.store'), ['name' => 'Projet interdit'])
            ->assertForbidden();

        $this->get(route('projects.edit', $project))
            ->assertForbidden();

        $this->put(route('projects.update', $project), ['name' => 'Nom modifié'])
            ->assertForbidden();

        $this->delete(route('projects.destroy', $project))
            ->assertForbidden();

        $this->post(route('projects.members.store', $project), ['user_id' => $employee->id])
            ->assertForbidden();

        $this->delete(route('projects.members.destroy', [$project, $employee]))
            ->assertForbidden();

        $this->assertDatabaseMissing('projects', ['name' => 'Projet interdit']);
        $this->assertSame($project->name, $project->fresh()->name);
    }

    public function test_employee_sees_only_assigned_projects(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $assignedProject = Project::factory()->for($manager, 'manager')->create(['name' => 'Projet assigné']);
        $otherProject = Project::factory()->for($manager, 'manager')->create(['name' => 'Projet étranger']);
        $assignedProject->users()->attach($employee);

        $this->actingAs($employee)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Projet assigné')
            ->assertDontSee('Projet étranger');
    }

    public function test_employee_sees_only_own_tasks_in_project_show(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $otherEmployee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach([$employee->id, $otherEmployee->id]);

        Task::factory()->for($project)->create([
            'title' => 'Tâche visible',
            'assigned_to' => $employee->id,
            'status' => TaskStatus::TODO->value,
        ]);
        Task::factory()->for($project)->create([
            'title' => 'Tâche masquée',
            'assigned_to' => $otherEmployee->id,
            'status' => TaskStatus::TODO->value,
        ]);

        $this->actingAs($employee)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Tâche visible')
            ->assertDontSee('Tâche masquée');
    }

    public function test_admin_sees_all_projects_but_cannot_modify_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create(['name' => 'Projet admin']);

        $this->actingAs($admin)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Projet admin');

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Projet admin');

        $this->get(route('projects.edit', $project))
            ->assertForbidden();

        $this->put(route('projects.update', $project), ['name' => 'Nom modifié'])
            ->assertForbidden();

        $this->delete(route('projects.destroy', $project))
            ->assertForbidden();

        $this->post(route('projects.members.store', $project), ['user_id' => $admin->id])
            ->assertForbidden();

        $this->assertSame('Projet admin', $project->fresh()->name);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('projects.index'))
            ->assertRedirect(route('login'));
    }

    public function test_inactive_user_is_refused(): void
    {
        $inactiveManager = User::factory()->create(['role' => 'manager', 'is_active' => false]);

        $this->actingAs($inactiveManager)
            ->get(route('projects.index'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_manager_can_attach_an_active_employee(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();

        $this->actingAs($manager)
            ->post(route('projects.members.store', $project), ['user_id' => $employee->id])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $employee->id,
        ]);
    }

    #[DataProvider('invalidMembers')]
    public function test_manager_cannot_attach_invalid_members(string $role, bool $isActive): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $candidate = User::factory()->create(['role' => $role, 'is_active' => $isActive]);
        $project = Project::factory()->for($manager, 'manager')->create();

        $this->actingAs($manager)
            ->from(route('projects.show', $project))
            ->followingRedirects()
            ->post(route('projects.members.store', $project), ['user_id' => $candidate->id])
            ->assertOk()
            ->assertSee('Seuls les employés actifs peuvent être ajoutés.');

        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $candidate->id,
        ]);
    }

    public function test_manager_cannot_attach_the_same_employee_twice(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->actingAs($manager)
            ->from(route('projects.show', $project))
            ->followingRedirects()
            ->post(route('projects.members.store', $project), ['user_id' => $employee->id])
            ->assertOk()
            ->assertSee('Cet employé est déjà membre du projet.');

        $this->assertSame(1, $project->users()->whereKey($employee->id)->count());
    }

    public function test_manager_can_detach_a_member(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->actingAs($manager)
            ->delete(route('projects.members.destroy', [$project, $employee]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $employee->id,
        ]);
    }

    public function test_non_owner_cannot_detach_a_member(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $otherManager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->actingAs($otherManager)
            ->delete(route('projects.members.destroy', [$project, $employee]))
            ->assertForbidden();

        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $employee->id,
        ]);
    }

    public function test_project_show_warns_about_pending_tasks_before_member_removal(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        Task::factory()->for($project)->create([
            'assigned_to' => $employee->id,
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);

        $this->actingAs($manager)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('tâche(s) non terminée(s)')
            ->assertSee('Le comportement des tâches reste inchangé.');
    }

    public function test_project_name_is_required(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($manager)
            ->from(route('projects.create'))
            ->followingRedirects()
            ->post(route('projects.store'), ['name' => ''])
            ->assertOk()
            ->assertSee('Le nom du projet est obligatoire.');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($manager)
            ->from(route('projects.create'))
            ->followingRedirects()
            ->post(route('projects.store'), [
                'name' => 'Projet avec dates invalides',
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-01',
            ])
            ->assertOk()
            ->assertSee('La date de fin doit être postérieure à la date de début.');

        $this->assertDatabaseMissing('projects', ['name' => 'Projet avec dates invalides']);
    }

    public function test_project_name_must_be_unique_per_manager(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        Project::factory()->for($manager, 'manager')->create(['name' => 'Projet unique']);

        $this->actingAs($manager)
            ->from(route('projects.create'))
            ->followingRedirects()
            ->post(route('projects.store'), ['name' => 'Projet unique'])
            ->assertOk()
            ->assertSee('Vous avez déjà un projet portant ce nom.');

        $this->assertSame(1, Project::where('name', 'Projet unique')->count());
    }
}
