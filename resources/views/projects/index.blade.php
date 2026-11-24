@extends('layouts.admin.template')

@section('title', 'Gestion des projets')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des projets</h1>
            <p class="text-secondary mb-0">Consultez les projets et suivez leur avancement.</p>
        </div>

        @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                Nouveau projet
            </a>
        @endcan
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Liste des projets</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Projet</th>
                            <th scope="col">Manager</th>
                            <th scope="col">Membres</th>
                            <th scope="col">Tâches</th>
                            <th scope="col">Avancement</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            @php
                                $projectProgress = $project->tasks_count > 0
                                    ? (int) round($project->completed_tasks_count * 100 / $project->tasks_count)
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="fw-semibold text-decoration-none">
                                        {{ $project->name }}
                                    </a>
                                    @if ($project->start_date || $project->end_date)
                                        <div class="small text-secondary mt-1">
                                            {{ $project->start_date?->format('d/m/Y') ?? '—' }}
                                            →
                                            {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $project->manager->name ?? 'Non affecté' }}</td>
                                <td>{{ $project->users->count() }}</td>
                                <td>{{ $project->tasks_count }}</td>
                                <td style="min-width: 160px;">
                                    <div class="progress" role="progressbar" aria-label="Avancement du projet"
                                        aria-valuenow="{{ $projectProgress }}" aria-valuemin="0" aria-valuemax="100"
                                        style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $projectProgress }}%"></div>
                                    </div>
                                    <small class="text-secondary">
                                        {{ $project->completed_tasks_count }}/{{ $project->tasks_count }} terminées
                                    </small>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                        Consulter
                                    </a>
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                            Modifier
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state icon="bi-folder2-open" title="Aucun projet"
                                        text="Aucun projet n'est disponible pour votre profil.">
                                        @can('create', App\Models\Project::class)
                                            <x-slot:action>
                                                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                                    Nouveau projet
                                                </a>
                                            </x-slot:action>
                                        @endcan
                                    </x-ui.empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($projects->hasPages())
                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
