<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\AttachProjectMemberRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        /** @var User $user */
        $user = auth()->user();

        $projects = Project::query()
            ->with(['manager:id,name', 'users:id,name'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query): void {
                    $query->where('status', TaskStatus::DONE->value);
                },
            ])
            ->when($user->isManager(), function ($query) use ($user): void {
                $query->where('manager_id', $user->id);
            })
            ->when($user->isEmployee(), function ($query) use ($user): void {
                $query->whereHas('users', function ($query) use ($user): void {
                    $query->whereKey($user->id);
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::create([
            ...$request->validated(),
            'manager_id' => $request->user()->id,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        /** @var User $user */
        $user = auth()->user();

        $project->loadMissing(['manager:id,name', 'users:id,name']);

        $tasksQuery = $project->tasks()->with('assignee:id,name');

        if ($user->isEmployee()) {
            $tasksQuery->where('assigned_to', $user->id);
        }

        $taskRows = $tasksQuery->orderByDesc('id')
            ->get()
            ->map(function (Task $task): array {
                return [
                    'task' => $task,
                    'is_overdue' => $task->due_date !== null
                        && $task->due_date->isBefore(today())
                        && $task->status !== TaskStatus::DONE->value,
                ];
            });

        $totalTasks = $taskRows->count();

        $completedTasks = $taskRows->filter(function (array $row): bool {
            return $row['task']->status === TaskStatus::DONE->value;
        })->count();

        $progress = $totalTasks > 0 ? (int) round($completedTasks * 100 / $totalTasks) : 0;

        $availableEmployees = collect();
        $memberPendingTasks = collect();

        if (Gate::allows('manageMembers', $project)) {
            $availableEmployees = User::query()
                ->where('role', 'employee')
                ->where('is_active', true)
                ->whereDoesntHave('projects', function ($query) use ($project): void {
                    $query->whereKey($project->id);
                })
                ->orderBy('name')
                ->get(['id', 'name'])
                ->mapWithKeys(function (User $employee): array {
                    return [$employee->id => $employee->name];
                });

            $memberPendingTasks = Task::query()
                ->where('project_id', $project->id)
                ->whereIn('assigned_to', $project->users->pluck('id'))
                ->where('status', '!=', TaskStatus::DONE->value)
                ->selectRaw('assigned_to, COUNT(*) as pending_count')
                ->groupBy('assigned_to')
                ->pluck('pending_count', 'assigned_to');
        }

        return view('projects.show', compact(
            'project',
            'taskRows',
            'totalTasks',
            'completedTasks',
            'progress',
            'availableEmployees',
            'memberPendingTasks'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Attach an active employee to the project.
     */
    public function attachMember(AttachProjectMemberRequest $request, Project $project): RedirectResponse
    {
        $project->users()->attach($request->integer('user_id'));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Employé ajouté au projet.');
    }

    /**
     * Detach an employee from the project.
     */
    public function detachMember(Project $project, User $user): RedirectResponse
    {
        Gate::authorize('manageMembers', $project);

        abort_unless($project->users()->whereKey($user->id)->exists(), 404);

        $project->users()->detach($user->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Employé retiré du projet.');
    }
}
