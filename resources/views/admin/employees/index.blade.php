@extends('layouts.admin.template')

@section('title', 'Gestion des employés')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des employés</h1>
            <p class="text-secondary mb-0">Gérer les employés de votre organisation.</p>
        </div>

        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            + Ajouter un employé
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

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
                                <td>{{ $employee->team->name ?? 'Aucune équipe' }}</td>
                                <td>{{ $employee->team->department->name ?? 'Aucun département' }}</td>
                                <td>
                                    @if ($employee->is_active)
                                        <span class="badge text-bg-success">Actif</span>
                                    @else
                                        <span class="badge text-bg-danger">Désactivé</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-primary">Voir</a>

                                    <a href="{{ route('admin.employees.edit', $employee) }}"
                                        class="btn btn-sm btn-outline-secondary">Modifier</a>

                                    @if ($employee->is_active)
                                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Voulez-vous vraiment désactiver cet employé ?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">Désactiver</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.employees.activate', ['employee' => $employee->id]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Voulez-vous vraiment réactiver cet employé ?');">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-outline-success">Réactiver</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-ui.empty-state icon="bi-people" title="Aucun employé trouvé"
                                        text="Ajoutez un premier employé pour commencer." />
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
