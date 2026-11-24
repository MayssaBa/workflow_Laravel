<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskUpdated;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Liste des tâches avec filtres.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);

        $user = auth()->user();

        $query = Task::with(['project', 'assignee', 'creator'])
            ->latest();

        // Le manager voit toutes les tâches.
        // L'employé voit uniquement ses tâches.
        if ($user->isEmployee()) {
            $query->where('assigned_to', $user->id);
        }

        // Filtre par projet.
        if ($request->filled('project_id') && $user->isManager()) {
            $query->where('project_id', $request->project_id);
        }

        // Filtre par employé.
        if ($request->filled('employee_id') && $user->isManager()) {
            $query->where('assigned_to', $request->employee_id);
        }

        // Filtre par statut.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par priorité.
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtre des tâches en retard.
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->whereDate('due_date', '<', now()->toDateString())
                ->where('status', '!=', 'done');
        }

        $tasks = $query->paginate(10)->withQueryString();

        $projects = Project::orderBy('name')->get();

        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tasks.index', compact(
            'tasks',
            'projects',
            'employees'
        ));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        Gate::authorize('create', Task::class);

        $projects = Project::orderBy('name')->get();

        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tasks.create', compact('projects', 'employees'));
    }

    /**
     * Enregistrer une nouvelle tâche.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Gate::authorize('create', Task::class);

        $validated = $request->validated();

        $relations = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'employee')
                        ->where('is_active', true);
                }),
            ],
        ]);

        $task = Task::create(array_merge(
            $validated,
            $relations,
            [
                'created_by' => auth()->id(),
                'status' => 'todo',
            ]
        ));

        // Notification envoyée à l'employé assigné.
        if ($task->assigned_to !== null) {
            $assignee = User::find($task->assigned_to);

            if ($assignee) {
                $assignee->notify(new TaskAssigned($task));
            }
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Consulter une tâche.
     */
    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        $task->load([
            'project',
            'assignee',
            'creator',
            'histories.user',
        ]);

        return view('tasks.show', compact('task'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        $projects = Project::orderBy('name')->get();

        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tasks.edit', compact('task', 'projects', 'employees'));
    }

    /**
     * Modifier les informations d'une tâche.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
            'due_date' => ['nullable', 'date'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'employee')
                        ->where('is_active', true);
                }),
            ],
        ]);

        // Remplir les nouvelles valeurs.
        $task->fill($validated);

        // Vérifier si au moins une valeur a changé.
        $hasChanges = $task->isDirty();

        // Enregistrer les modifications.
        $task->save();

        // Notifier uniquement si une modification existe.
        if ($hasChanges && $task->assigned_to !== null) {
            $assignee = User::find($task->assigned_to);

            if ($assignee) {
                $assignee->notify(new TaskUpdated($task));
            }
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Tâche modifiée avec succès.');
    }

    /**
     * Modifier le statut d'une tâche.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('updateStatus', $task);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['todo', 'in_progress', 'blocked', 'done']),
            ],
            'blocked_reason' => [
                'required_if:status,blocked',
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'blocked_reason.required_if' =>
                'Vous devez expliquer pourquoi cette tâche est bloquée.',
            'blocked_reason.max' =>
                'La justification ne doit pas dépasser 5000 caractères.',
        ]);

        $task->update([
            'status' => $validated['status'],
            'blocked_reason' => $validated['status'] === 'blocked'
                ? $validated['blocked_reason']
                : null,
        ]);

        return back()->with(
            'success',
            'Statut de la tâche mis à jour.'
        );
    }

    /**
     * Suppression logique d'une tâche.
     */
    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'La tâche a été supprimée avec succès.');
    }
}
