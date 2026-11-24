@extends('layouts.admin.template')

@section('title', 'Gestion des équipes')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des équipes</h1>
            <p class="text-secondary mb-0">Gérer les équipes de votre organisation.</p>
        </div>

        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">
            + Ajouter une équipe
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Liste des équipes</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nom de l'équipe</th>
                            <th scope="col">Département</th>
                            <th scope="col">Nombre d'employés</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teams as $team)
                            <tr>
                                <td class="fw-semibold">{{ $team->name }}</td>
                                <td>{{ $team->department?->name ?? 'Non attribué' }}</td>
                                <td>{{ $team->users_count }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.teams.show', $team) }}"
                                        class="btn btn-sm btn-outline-primary">Voir</a>

                                    <a href="{{ route('admin.teams.edit', $team) }}"
                                        class="btn btn-sm btn-outline-secondary">Modifier</a>

                                    <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer cette équipe ?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui.empty-state icon="bi-person-workspace" title="Aucune équipe trouvée"
                                        text="Ajoutez une première équipe pour commencer." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($teams->hasPages())
                <div class="mt-4">
                    {{ $teams->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
