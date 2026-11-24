
@extends('layouts.admin.template')

@section('content')

<div class="container-fluid">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Gestion des tâches</h2>
            <p class="text-muted mb-0">
                Liste des tâches de votre espace de travail.
            </p>
        </div>

        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                + Nouvelle tâche
            </a>
        @endcan
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtres de recherche</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('tasks.index') }}" method="GET">

                <div class="row">

                    @if(auth()->user()->isManager())

                        <div class="col-md-3 mb-3">
                            <label for="project_id" class="form-label">
                                Projet
                            </label>

                            <select name="project_id"
                                    id="project_id"
                                    class="form-select">

                                <option value="">Tous les projets</option>

                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="employee_id" class="form-label">
                                Employé
                            </label>

                            <select name="employee_id"
                                    id="employee_id"
                                    class="form-select">

                                <option value="">Tous les employés</option>

                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    @endif

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">
                            Statut
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>

                            <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>
                                À faire
                            </option>

                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                En cours
                            </option>

                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>
                                Bloquée
                            </option>

                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>
                                Terminée
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="priority" class="form-label">
                            Priorité
                        </label>

                        <select name="priority" id="priority" class="form-select">
                            <option value="">Toutes les priorités</option>

                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>
                                Faible
                            </option>

                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>
                                Moyenne
                            </option>

                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>
                                Élevée
                            </option>

                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>
                                Urgente
                            </option>
                        </select>
                    </div>

                </div>

                <div class="form-check mb-3">
                    <input type="checkbox"
                           name="overdue"
                           value="1"
                           id="overdue"
                           class="form-check-input"
                           {{ request('overdue') == '1' ? 'checked' : '' }}>

                    <label for="overdue" class="form-check-label">
                        Afficher uniquement les tâches en retard
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Filtrer
                </button>

                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    Réinitialiser
                </a>

            </form>
        </div>
    </div>

    {{-- Tableau des tâches --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                Liste des tâches ({{ $tasks->total() }})
            </h5>
        </div>

        <div class="card-body">

            @if($tasks->count() > 0)

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Tâche</th>
                                <th>Projet</th>
                                <th>Responsable</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date limite</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($tasks as $task)

                                <tr>
                                    <td>
                                        <strong>{{ $task->title }}</strong>

                                        @if($task->status === 'blocked')
                                            <br>
                                            <small class="text-danger">
                                                Tâche bloquée
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $task->project?->name ?? 'Aucun projet' }}
                                    </td>

                                    <td>
                                        {{ $task->assignee?->name ?? 'Non assignée' }}
                                    </td>

                                    <td>
                                        @switch($task->priority)
                                            @case('low')
                                                <span class="badge bg-secondary">Faible</span>
                                                @break

                                            @case('medium')
                                                <span class="badge bg-info text-dark">Moyenne</span>
                                                @break

                                            @case('high')
                                                <span class="badge bg-warning text-dark">Élevée</span>
                                                @break

                                            @case('urgent')
                                                <span class="badge bg-danger">Urgente</span>
                                                @break
                                        @endswitch
                                    </td>

                                    <td>
                                        @switch($task->status)
                                            @case('todo')
                                                <span class="badge bg-secondary">À faire</span>
                                                @break

                                            @case('in_progress')
                                                <span class="badge bg-primary">En cours</span>
                                                @break

                                            @case('blocked')
                                                <span class="badge bg-danger">Bloquée</span>
                                                @break

                                            @case('done')
                                                <span class="badge bg-success">Terminée</span>
                                                @break
                                        @endswitch
                                    </td>

                                    <td>
                                        {{ $task->due_date?->format('d/m/Y') ?? 'Non définie' }}

                                        @if($task->due_date &&
                                            $task->due_date->isPast() &&
                                            $task->status !== 'done')
                                            <br>
                                            <small class="text-danger">
                                                En retard
                                            </small>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="d-flex gap-1">

                                            {{-- Voir --}}
                                            <a href="{{ route('tasks.show', $task) }}"
                                               class="btn btn-sm btn-info">
                                                Voir
                                            </a>

                                            {{-- Modifier --}}
                                            @can('update', $task)
                                                <a href="{{ route('tasks.edit', $task) }}"
                                                   class="btn btn-sm btn-warning">
                                                    Modifier
                                                </a>
                                            @endcan

                                            {{-- Supprimer --}}
                                            @can('delete', $task)
                                                <form action="{{ route('tasks.destroy', $task) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Voulez-vous vraiment supprimer cette tâche ?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger">
                                                        Supprimer
                                                    </button>

                                                </form>
                                            @endcan

                                        </div>
                                    </td>

                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>

            @else

                <div class="alert alert-info mb-0">
                    Aucune tâche trouvée.
                </div>

            @endif

        </div>
    </div>

</div>

@endsection
