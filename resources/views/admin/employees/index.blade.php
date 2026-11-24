@extends('layouts.admin.template')

@section('title', 'Gestion des employés')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des employés</h1>
            <p class="text-secondary mb-0">Gérez les employés de votre organisation.</p>
        </div>

        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            Ajouter un employé
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Liste des employés</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nom</th>
                            <th scope="col">Email</th>
                            <th scope="col">Téléphone</th>
                            <th scope="col">Équipe</th>
                            <th scope="col">Département</th>
                            <th scope="col">Statut</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="fw-semibold">{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone ?? 'Non renseigné' }}</td>
                                <td>{{ $employee->team->name ?? 'Non affecté' }}</td>
                                <td>{{ $employee->team->department->name ?? 'Non affecté' }}</td>
                                <td>
                                    <x-status-badge :status="$employee->is_active ? 'active' : 'inactive'" />
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-primary">Consulter</a>

                                    <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true"
                                        title="bientôt disponible">Modifier</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-ui.empty-state icon="bi-people" title="Aucun employé"
                                        text="Ajoutez un premier employé pour commencer.">
                                        <x-slot:action>
                                            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm">
                                                Ajouter un employé
                                            </a>
                                        </x-slot:action>
                                    </x-ui.empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
