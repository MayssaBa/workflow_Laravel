@extends('layouts.admin.template')

@section('title', $project->name)

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $project->name }}</h1>
            <p class="text-secondary mb-0">Détails du projet, tâches et membres.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" disabled title="bientôt disponible">
                Nouvelle tâche
            </button>
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">Modifier</a>
            @endcan
            @can('delete', $project)
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#delete-project-modal">Supprimer</button>
            @endcan
        </div>
    </div>

    @can('delete', $project)
        <x-ui.modal id="delete-project-modal" title="Supprimer le projet ?"
            description="Le projet sera masqué de la liste mais conservé en base. Cette action est irréversible depuis l'interface."
            formAction="{{ route('projects.destroy', $project) }}" method="DELETE" confirmLabel="Supprimer"
            confirmVariant="danger" />
    @endcan

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Informations</h2>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-secondary small">Manager</div>
                    <div class="fw-semibold">{{ $project->manager->name ?? 'Non affecté' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-secondary small">Date de début</div>
                    <div class="fw-semibold">{{ $project->start_date?->format('d/m/Y') ?? 'Non renseignée' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-secondary small">Date de fin</div>
                    <div class="fw-semibold">{{ $project->end_date?->format('d/m/Y') ?? 'Non renseignée' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-secondary small">Avancement</div>
                    <div class="fw-semibold">{{ $completedTasks }}/{{ $totalTasks }} terminées</div>
                </div>
                @if ($project->description)
                    <div class="col-12">
                        <div class="text-secondary small">Description</div>
                        <div>{{ $project->description }}</div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="progress" role="progressbar" aria-label="Avancement du projet"
                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                        <div class="progress-bar" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Tâches du projet</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Titre</th>
                            <th scope="col">Employé</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Priorité</th>
                            <th scope="col">Date limite</th>
                            <th scope="col">Retard</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taskRows as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row['task']->title }}</td>
                                <td>{{ $row['task']->assignee->name ?? 'Non affecté' }}</td>
                                <td><x-status-badge :status="$row['task']->status" /></td>
                                <td><x-status-badge :status="$row['task']->priority" /></td>
                                <td>{{ $row['task']->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    @if ($row['is_overdue'])
                                        <span class="badge text-bg-danger">En retard</span>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state icon="bi-check2-square" title="Aucune tâche"
                                        text="Aucune tâche n'est disponible pour ce projet." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Membres du projet</h2>

            @can('manageMembers', $project)
                @if ($availableEmployees->isNotEmpty())
                    <form method="POST" action="{{ route('projects.members.store', $project) }}"
                        class="row g-2 align-items-end mb-4" data-loading>
                        @csrf
                        <div class="col-md-9">
                            <x-form.select field="user_id" label="Ajouter un employé" :options="$availableEmployees"
                                placeholder="Sélectionner un employé actif" :required="true" />
                        </div>
                        <div class="col-md-3">
                            <x-form.submit label="Ajouter" />
                        </div>
                    </form>
                @else
                    <p class="text-secondary mb-4">Aucun employé actif disponible.</p>
                @endif
            @endcan

            @forelse ($project->users as $member)
                @php
                    $pendingCount = (int) ($memberPendingTasks[$member->id] ?? 0);
                    $removalDescription = $pendingCount > 0
                        ? "Attention : {$pendingCount} tâche(s) non terminée(s) resteront associées à ce projet. Le comportement des tâches reste inchangé."
                        : "Ce membre n'a aucune tâche non terminée dans ce projet.";
                @endphp
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 py-3 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <x-ui.avatar :user="$member" size="sm" />
                        <div>
                            <div class="fw-semibold">{{ $member->name }}</div>
                            @can('manageMembers', $project)
                                @if ($pendingCount > 0)
                                    <div class="small text-warning-emphasis">
                                        {{ $pendingCount }} tâche(s) non terminée(s) dans ce projet
                                    </div>
                                @endif
                            @endcan
                        </div>
                    </div>

                    @can('manageMembers', $project)
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#remove-member-{{ $member->id }}">Retirer</button>

                        <x-ui.modal id="remove-member-{{ $member->id }}"
                            title="Retirer {{ $member->name }} du projet ?"
                            :description="$removalDescription"
                            formAction="{{ route('projects.members.destroy', [$project, $member]) }}" method="DELETE"
                            confirmLabel="Retirer" confirmVariant="danger" />
                    @endcan
                </div>
            @empty
                <x-ui.empty-state icon="bi-people" title="Aucun membre"
                    text="Aucun employé n'est affecté à ce projet." />
            @endforelse
        </div>
    </div>
@endsection
