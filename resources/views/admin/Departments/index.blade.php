@extends('layouts.admin.template')

@section('title', 'Gestion des départements')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des départements</h1>
            <p class="text-secondary mb-0">Gérer les départements de votre organisation.</p>
        </div>

        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            + Ajouter un département
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Liste des départements</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nom du département</th>
                            <th scope="col">Nombre d'équipes</th>
                            <th scope="col">Date de création</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td class="fw-semibold">{{ $department->name }}</td>
                                <td>{{ $department->teams_count }}</td>
                                <td>{{ $department->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.departments.show', $department) }}"
                                        class="btn btn-sm btn-outline-primary">Voir</a>

                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                        class="btn btn-sm btn-outline-secondary">Modifier</a>

                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer ce département ?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui.empty-state icon="bi-diagram-3" title="Aucun département trouvé"
                                        text="Ajoutez un premier département pour commencer." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($departments->hasPages())
                <div class="mt-4">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
