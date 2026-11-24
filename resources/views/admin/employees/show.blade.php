@extends('layouts.admin.template')

@section('title', 'Détails de l\'employé')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $employee->name }}</h1>
            <p class="text-secondary mb-0">Informations détaillées de l'employé.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true"
                title="bientôt disponible">Modifier</span>

            <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Informations personnelles</h2>

            <div class="row g-4">
                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Nom complet</p>
                    <p class="fw-semibold mb-0">{{ $employee->name }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Adresse email</p>
                    <p class="fw-semibold mb-0">{{ $employee->email }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Téléphone</p>
                    <p class="fw-semibold mb-0">{{ $employee->phone ?? 'Non renseigné' }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Rôle</p>
                    <p class="fw-semibold mb-0">{{ ucfirst($employee->role) }}</p>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h5 fw-semibold mb-3">Affectation organisationnelle</h2>

            <div class="row g-4">
                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Département</p>
                    <p class="fw-semibold mb-0">{{ $employee->team->department->name ?? 'Non affecté' }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary small mb-1">Équipe</p>
                    <p class="fw-semibold mb-0">{{ $employee->team->name ?? 'Non affecté' }}</p>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h5 fw-semibold mb-3">Statut du compte</h2>

            <x-status-badge :status="$employee->is_active ? 'active' : 'inactive'" />

            <div class="row g-3 mt-3 text-secondary small">
                <div class="col-md-6">
                    <p class="mb-0">Créé le : {{ $employee->created_at?->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0">Dernière modification : {{ $employee->updated_at?->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
