
@extends('layouts.admin.template')

@section('content')

<div class="alert alert-warning">
    TEST: TASK DETAILS VIEW IS DISPLAYED
</div>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Détails de la tâche</h2>

        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
            Retour à la liste
        </a>
    </div>

    {{-- Messages de succès --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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

    {{-- Informations générales --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informations générales</h5>
        </div>

        <div class="card-body">

            <h4>{{ $task->title }}</h4>
            <hr>

            <p>
                <strong>Description :</strong><br>
                {{ $task->description ?: 'Aucune description' }}
            </p>

            <p>
                <strong>Projet :</strong>
                {{ $task->project?->name ?? 'Aucun projet' }}
            </p>

            <p>
                <strong>Responsable :</strong>
                {{ $task->assignee?->name ?? 'Non assignée' }}
            </p>

            <p>
                <strong>Créée par :</strong>
                {{ $task->creator?->name ?? 'Utilisateur inconnu' }}
            </p>

            <p>
                <strong>Priorité :</strong>
                {{ ucfirst($task->priority) }}
            </p>

            <p>
                <strong>Date limite :</strong>
                {{ $task->due_date?->format('d/m/Y') ?? 'Non définie' }}
            </p>

            <p>
                <strong>Statut :</strong>

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

                    @default
                        <span class="badge bg-secondary">
                            {{ $task->status }}
                        </span>
                @endswitch
            </p>

            @if($task->status === 'blocked')
                <div class="alert alert-danger mt-3">
                    <strong>Raison du blocage :</strong>
                    <p class="mb-0">
                        {{ $task->blocked_reason ?: 'Aucune justification enregistrée.' }}
                    </p>
                </div>
            @endif

        </div>
    </div>

    {{-- Modification du statut --}}
    @can('updateStatus', $task)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Modifier le statut</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Nouveau statut
                        </label>

                        <select name="status" id="status" class="form-select" required>
                            <option value="todo"
                                {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>
                                À faire
                            </option>

                            <option value="in_progress"
                                {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>
                                En cours
                            </option>

                            <option value="blocked"
                                {{ old('status', $task->status) === 'blocked' ? 'selected' : '' }}>
                                Bloquée
                            </option>

                            <option value="done"
                                {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>
                                Terminée
                            </option>
                        </select>
                    </div>

                    <div class="mb-3" id="blockedReasonContainer">
                        <label for="blocked_reason" class="form-label">
                            Justification du blocage
                        </label>

                        <textarea
                            name="blocked_reason"
                            id="blocked_reason"
                            class="form-control"
                            rows="4"
                            maxlength="5000"
                            placeholder="Expliquez pourquoi cette tâche est bloquée..."
                        >{{ old('blocked_reason', $task->blocked_reason) }}</textarea>

                        <small class="text-muted">
                            Obligatoire lorsque le statut est « Bloquée ».
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer le statut
                    </button>
                </form>

            </div>
        </div>
    @endcan

    {{-- Actions réservées aux utilisateurs autorisés --}}
    @can('update', $task)
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning mb-3">
            Modifier la tâche
        </a>
    @endcan

    @can('delete', $task)
        <form action="{{ route('tasks.destroy', $task) }}"
            method="POST"
            class="d-inline"
            onsubmit="return confirm('Voulez-vous vraiment supprimer cette tâche ?')">

            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger mb-3">
                Supprimer la tâche
            </button>
        </form>
    @endcan

    {{-- Historique de la tâche --}}

    </div>
    <div class="card shadow-sm mt-4 mb-4">
    <div class="card-header">
        <h5 class="mb-0">Task History</h5>
    </div>

    <div class="card-body">
        @forelse ($task->histories as $history)
            <div class="border-bottom py-2">
                <strong>{{ $history->user?->name ?? 'System' }}</strong>

                {{ ucfirst(str_replace('_', ' ', $history->action)) }}

                @if ($history->field)
                    — {{ $history->field }}
                @endif

                <br>

                <small class="text-muted">
                    {{ $history->created_at?->format('d/m/Y H:i') }}
                </small>

                @if ($history->old_value !== null || $history->new_value !== null)
                    <div>
                        Old value: {{ $history->old_value ?? '—' }}
                        <br>
                        New value: {{ $history->new_value ?? '—' }}
                    </div>
                @endif
            </div>
        @empty
            <p class="mb-0">No history recorded for this task yet.</p>
        @endforelse
    </div>
</div>

</div>

{{-- JavaScript : afficher la justification uniquement si la tâche est bloquée --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const status = document.getElementById('status');
        const reasonContainer = document.getElementById('blockedReasonContainer');
        const reasonField = document.getElementById('blocked_reason');

        function toggleBlockedReason() {
            if (!status || !reasonContainer || !reasonField) {
                return;
            }

            const isBlocked = status.value === 'blocked';

            reasonContainer.style.display = isBlocked ? 'block' : 'none';
            reasonField.required = isBlocked;
        }

        if (status) {
            status.addEventListener('change', toggleBlockedReason);
            toggleBlockedReason();
        }

    });
</script>

@endsection

