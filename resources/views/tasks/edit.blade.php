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

                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach

                            </select>
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

@endsection