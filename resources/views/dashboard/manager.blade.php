@extends('layouts.admin.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Tableau de bord Manager</h1>

        <p class="text-secondary mb-0">
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            · connecté en tant que {{ auth()->user()->name }}
        </p>
    </div>

    <div class="row g-3">

        {{-- Projets actifs --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('projects.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Projets actifs" :value="$activeProjects" icon="bi-folder2-open" />
            </a>
        </div>

        {{-- Tâches en retard --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('tasks.index', ['overdue' => 1]) }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Tâches en retard" :value="$overdueTasks" icon="bi-clock-history" color="danger" />
            </a>
        </div>

        {{-- Problèmes ouverts --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('manager.issues.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Problèmes ouverts" :value="$openIssues" icon="bi-exclamation-circle" color="warning" />
            </a>
        </div>

        {{-- Demandes à traiter --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('requests.index', ['tab' => 'received']) }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Demandes à traiter" :value="$openRequests" icon="bi-chat-left-text" color="warning" />
            </a>
        </div>

    </div>

    {{-- Progression des projets --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Progression des projets</h2>
        </div>

        <div class="card-body">
            @forelse ($projectProgress as $project)
                @php
                    $total = $project->tasks_count;
                    $completed = $project->completed_tasks_count;
                    $percentage = $total > 0
                        ? round(($completed / $total) * 100)
                        : 0;
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>{{ $project->name }}</strong>
                        <span class="text-secondary">
                            {{ $completed }} / {{ $total }} tâches ({{ $percentage }}%)
                        </span>
                    </div>

                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;"
                            aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-secondary mb-0">Aucun projet disponible.</p>
            @endforelse
        </div>
    </div>

    {{-- Charge de travail par employé --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Charge de travail par employé</h2>
        </div>

        <div class="card-body">
            @forelse ($employeeWorkload as $employee)
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div>
                        <strong>{{ $employee->name }}</strong>
                        <div class="text-secondary small">
                            {{ $employee->email }}
                        </div>
                    </div>

                    <span class="badge bg-primary">
                        {{ $employee->active_tasks_count }} tâches actives
                    </span>
                </div>
            @empty
                <p class="text-secondary mb-0">Aucun employé actif disponible.</p>
            @endforelse
        </div>
    </div>
@endsection