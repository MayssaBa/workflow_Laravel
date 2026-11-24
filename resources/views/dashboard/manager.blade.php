
@extends('layouts.admin.template')

@section('title', 'Tableau de bord manager')

@section('content')

    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Welcome to Manager Dashboard</h1>

        <p class="text-secondary mb-0">
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            · connecté en tant que {{ auth()->user()->name }}
        </p>
    </div>

    <div class="row g-3">

        {{-- Mes projets --}}
        <div class="col-sm-6 col-xl-4">
            <x-stat-card
                title="Mes projets"
                :value="rescue(fn () => auth()->user()->managedProjects()->count(), 0, false)"
                icon="bi-folder2-open"
            />
        </div>

        {{-- Tâches créées : carte cliquable --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('tasks.index') }}"
            php artisan view:cacheclass="text-decoration-none text-dark">

                <x-stat-card
                    title="Tâches créées"
                    :value="rescue(fn () => auth()->user()->createdTasks()->count(), 0, false)"
                    icon="bi-check2-square"
                    color="success"
                />

            </a>
        </div>

        {{-- Demandes à traiter --}}
        <div class="col-sm-6 col-xl-4">
            <x-stat-card
                title="Demandes à traiter"
                :value="rescue(fn () => \App\Models\InternalRequest::where('recipient_id', auth()->id())->where('status', 'open')->count(), 0, false)"
                icon="bi-chat-left-text"
                color="warning"
            />
        </div>

    </div>

@endsection
