@extends('layouts.admin.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Tableau de bord Administrateur</h1>

        <p class="text-secondary mb-0">
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            · connecté en tant que {{ auth()->user()->name }}
        </p>
    </div>

    {{-- Statistiques principales --}}
    <div class="row g-3">

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Employés actifs" :value="$activeEmployees" icon="bi-people" />
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.departments.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Départements" :value="$departments" icon="bi-diagram-3" color="info" />
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.teams.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Équipes" :value="$teams" icon="bi-person-workspace" color="success" />
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('requests.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Demandes ouvertes" :value="$openRequests" icon="bi-chat-left-text" color="warning" />
            </a>
        </div>

    </div>

    {{-- Employés par département --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Employés par département</h2>
        </div>

        <div class="card-body">
            @forelse ($employeesByDepartment as $department)
                @php
                    $employeeCount = $department->teams->sum(
                        fn($team) => $team->users->count()
                    );
                @endphp

                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div>
                        <strong>{{ $department->name }}</strong>
                        <div class="text-secondary small">
                            {{ $department->teams_count }}
                            {{ $department->teams_count === 1 ? 'équipe' : 'équipes' }}
                        </div>
                    </div>

                    <span class="badge bg-primary">
                        {{ $employeeCount }}
                        {{ $employeeCount === 1 ? 'employé' : 'employés' }}
                    </span>
                </div>
            @empty
                <p class="text-secondary mb-0">
                    Aucun département disponible.
                </p>
            @endforelse
        </div>
    </div>

    {{-- Demandes par statut --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Demandes par statut</h2>
        </div>

        <div class="card-body">
            @php
                $statusLabels = [
                    'open' => 'Ouvertes',
                    'answered' => 'Répondues',
                    'closed' => 'Clôturées',
                ];

                $statusColors = [
                    'open' => 'warning',
                    'answered' => 'info',
                    'closed' => 'success',
                ];
            @endphp

            @forelse ($requestsByStatus as $requestStatus)
                @php
                    $status = $requestStatus->status;
                    $label = $statusLabels[$status] ?? ucfirst($status);
                    $color = $statusColors[$status] ?? 'secondary';
                @endphp

                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <span>{{ $label }}</span>

                    <span class="badge bg-{{ $color }}">
                        {{ $requestStatus->total }}
                    </span>
                </div>
            @empty
                <p class="text-secondary mb-0">
                    Aucune demande enregistrée.
                </p>
            @endforelse
        </div>
    </div>
@endsection