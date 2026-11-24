<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaskAssignmentValidationTest extends TestCase
{
    use RefreshDatabase;

    private const MEMBERSHIP_ERROR = "Cet employé ne fait pas partie du projet. Ajoutez-le d'abord aux membres.";

    /** @return array<string, array{string, bool}> */
    public static function invalidAssignees(): array
    {
        return [
            'manager' => ['manager', true],
            'admin' => ['admin', true],
            'inactive employee' => ['employee', false],
        ];
    }

    public function test_manager_can_assign_an_active_project_member(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($employee);

        $this->actingAs($manager)
            ->post(route('tasks.store'), $this->taskPayload($project, $employee->id))
            ->assertSessionHasNoErrors()
            ->assertRedirectContains('/tasks/');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tâche de test',
            'project_id' => $project->id,
            'assigned_to' => $employee->id,
        ]);
    }

    public function test_manager_cannot_assign_an_employee_who_is_not_a_project_member(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();

        $this->actingAs($manager)
            ->post(route('tasks.store'), $this->taskPayload($project, $employee->id))
            ->assertSessionHasErrors(['assigned_to' => self::MEMBERSHIP_ERROR]);

        $this->assertDatabaseCount('tasks', 0);
    }

    #[DataProvider('invalidAssignees')]
    public function test_manager_cannot_assign_an_invalid_user_even_when_attached(string $role, bool $isActive): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $candidate = User::factory()->create(['role' => $role, 'is_active' => $isActive]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($candidate);

        $this->actingAs($manager)
            ->post(route('tasks.store'), $this->taskPayload($project, $candidate->id))
            ->assertSessionHasErrors(['assigned_to' => self::MEMBERSHIP_ERROR]);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_manager_cannot_assign_an_employee_without_a_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $payload = $this->taskPayload(null, $employee->id);

        $this->actingAs($manager)
            ->post(route('tasks.store'), $payload)
            ->assertSessionHasErrors(['assigned_to' => self::MEMBERSHIP_ERROR]);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_manager_cannot_update_a_task_with_a_non_member_employee(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $member = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $outsider = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($member);

        $task = Task::factory()->for($project)->create(['assigned_to' => $member->id]);

        $this->actingAs($manager)
            ->put(route('tasks.update', $task), $this->taskPayload($project, $outsider->id))
            ->assertSessionHasErrors(['assigned_to' => self::MEMBERSHIP_ERROR]);

        $this->assertSame($member->id, $task->fresh()->assigned_to);
    }

    public function test_manager_cannot_move_a_task_to_a_project_where_the_employee_is_not_a_member(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $originalProject = Project::factory()->for($manager, 'manager')->create();
        $otherProject = Project::factory()->for($manager, 'manager')->create();
        $originalProject->users()->attach($employee);

        $task = Task::factory()->for($originalProject)->create(['assigned_to' => $employee->id]);

        $this->actingAs($manager)
            ->put(route('tasks.update', $task), $this->taskPayload($otherProject, $employee->id))
            ->assertSessionHasErrors(['assigned_to' => self::MEMBERSHIP_ERROR]);

        $this->assertSame($originalProject->id, $task->fresh()->project_id);
    }

    public function test_task_forms_list_only_active_members_of_the_selected_project(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $member = User::factory()->create([
            'name' => 'Membre Visible',
            'role' => 'employee',
            'is_active' => true,
        ]);
        $outsider = User::factory()->create([
            'name' => 'Employé Hors Projet',
            'role' => 'employee',
            'is_active' => true,
        ]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $project->users()->attach($member);

        $task = Task::factory()->for($project)->create(['assigned_to' => $member->id]);

        $this->actingAs($manager)
            ->get(route('tasks.create'))
            ->assertOk()
            ->assertSee('Membre Visible')
            ->assertDontSee('Employé Hors Projet');

        $this->get(route('tasks.edit', $task))
            ->assertOk()
            ->assertSee('Membre Visible')
            ->assertDontSee('Employé Hors Projet');
    }

    public function test_empty_project_message_links_to_members_only_for_the_project_manager(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $otherManager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $project = Project::factory()->for($manager, 'manager')->create();
        $task = Task::factory()->for($project)->create(['assigned_to' => null]);

        $response = $this->actingAs($manager)
            ->get(route('tasks.edit', $task))
            ->assertOk()
            ->assertSee('Aucun employé dans ce projet');

        $link = $this->findElementById($response->getContent(), 'project-members-link');

        $this->assertNotNull($link);
        $this->assertFalse($link->hasAttribute('hidden'));
        $this->assertSame(route('projects.show', $project), $link->getAttribute('href'));

        $response = $this->actingAs($otherManager)
            ->get(route('tasks.edit', $task))
            ->assertOk()
            ->assertSee('Aucun employé dans ce projet');

        $link = $this->findElementById($response->getContent(), 'project-members-link');

        $this->assertNotNull($link);
        $this->assertTrue($link->hasAttribute('hidden'));
    }

    /** @return array{title: string, description: string, priority: string, project_id: int|null, assigned_to: int} */
    private function taskPayload(?Project $project, int $assignedTo): array
    {
        return [
            'title' => 'Tâche de test',
            'description' => 'Description de test',
            'priority' => 'medium',
            'project_id' => $project?->id,
            'assigned_to' => $assignedTo,
        ];
    }

    private function findElementById(string $html, string $id): ?DOMElement
    {
        $document = new DOMDocument;

        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $nodes = (new DOMXPath($document))->query('//*[@id="'.$id.'"]');

        return $nodes === false ? null : $nodes->item(0);
    }
}
