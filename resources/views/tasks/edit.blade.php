@extends('layouts.admin.template')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifier une tâche</h2>

            <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                Retour aux détails
            </a>
        </div>

        {{-- Erreurs de validation --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $selectedProject = $projects->firstWhere('id', old('project_id', $task->project_id));
            $selectedEmployees = $selectedProject !== null
                ? ($employeesByProject[$selectedProject->id] ?? [])
                : [];
            $canManageSelectedProject = $selectedProject !== null
                && in_array((int) $selectedProject->id, $manageableProjectIds, true);
        @endphp

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Modifier les informations</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Titre --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Titre de la tâche *
                        </label>

                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ old('title', $task->title) }}" maxlength="255" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>

                        <textarea name="description" id="description" class="form-control"
                            rows="4">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="row">

                        {{-- Projet --}}
                        <div class="col-md-6 mb-3">
                            <label for="project_id" class="form-label">
                                Projet
                            </label>

                            <select name="project_id" id="project_id" class="form-select">

                                <option value="">Aucun projet</option>

                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Responsable --}}
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">
                                Responsable
                            </label>

                            <select name="assigned_to" id="assigned_to" class="form-select">

                                <option value="">Non assignée</option>

                                @foreach($selectedEmployees as $id => $name)
                                    <option value="{{ $id }}" {{ old('assigned_to', $task->assigned_to) == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach

                            </select>

                            <div id="project-employee-empty" class="alert alert-warning py-2 small mt-2" @if($selectedProject !== null && ! empty($selectedEmployees)) hidden @endif>
                                <span id="project-employee-empty-text">
                                    {{ $selectedProject !== null ? 'Aucun employé dans ce projet' : 'Sélectionnez un projet pour afficher ses employés.' }}
                                </span>

                                <a id="project-members-link" href="{{ $selectedProject !== null ? route('projects.show', $selectedProject) : '#' }}" class="alert-link" @if(! $canManageSelectedProject) hidden @endif>
                                    Ajouter des membres
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        {{-- Priorité --}}
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">
                                Priorité *
                            </label>

                            <select name="priority" id="priority" class="form-select" required>

                                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>
                                    Faible
                                </option>

                                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>
                                    Moyenne
                                </option>

                                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>
                                    Élevée
                                </option>

                                <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>
                                    Urgente
                                </option>

                            </select>
                        </div>

                        {{-- Date limite --}}
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">
                                Date limite
                            </label>

                            <input type="date" name="due_date" id="due_date" class="form-control"
                                value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                        </div>

                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>

                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>

@push('scripts')
<script>
    const employeesByProject = {{ Illuminate\Support\Js::from($employeesByProject) }};
    const manageableProjectIds = {{ Illuminate\Support\Js::from($manageableProjectIds) }};
    const projectBaseUrl = {{ Illuminate\Support\Js::from(route('projects.index')) }};
    const initialAssignedTo = {{ Illuminate\Support\Js::from((string) old('assigned_to', $task->assigned_to)) }};
    const projectSelect = document.getElementById('project_id');
    const employeeSelect = document.getElementById('assigned_to');
    const emptyState = document.getElementById('project-employee-empty');
    const emptyText = document.getElementById('project-employee-empty-text');
    const memberLink = document.getElementById('project-members-link');
    let isInitialRender = true;

    function updateEmployeeOptions() {
        const projectId = projectSelect.value;
        const employees = projectId === '' ? {} : (employeesByProject[projectId] ?? {});
        const currentEmployeeId = employeeSelect.value;
        const preferredEmployeeId = isInitialRender ? initialAssignedTo : currentEmployeeId;

        employeeSelect.replaceChildren();

        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = 'Non assignée';
        employeeSelect.append(emptyOption);

        Object.entries(employees).forEach(([id, name]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            employeeSelect.append(option);
        });

        if (employees[preferredEmployeeId] !== undefined) {
            employeeSelect.value = preferredEmployeeId;
        }

        const hasMembers = Object.keys(employees).length > 0;
        emptyState.hidden = projectId !== '' && hasMembers;
        emptyText.textContent = projectId === ''
            ? 'Sélectionnez un projet pour afficher ses employés.'
            : 'Aucun employé dans ce projet';

        const canManage = manageableProjectIds.includes(Number(projectId));
        memberLink.hidden = ! canManage || projectId === '';
        memberLink.href = projectId === '' ? '#' : projectBaseUrl + '/' + projectId;

        isInitialRender = false;
    }

    projectSelect.addEventListener('change', updateEmployeeOptions);
    updateEmployeeOptions();
</script>
@endpush

@endsection