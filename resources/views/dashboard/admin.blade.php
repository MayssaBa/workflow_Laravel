@extends('layouts.admin.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Tableau de bord</h1>
            <p class="text-secondary mb-0">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }} · connecté en tant que
                {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Utilisateurs" :value="rescue(fn() => \App\Models\User::count(), 0, false)"
                    icon="bi-people" />
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card title="Départements" :value="rescue(fn() => \App\Models\Department::count(), 0, false)"
                icon="bi-diagram-3" color="info" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card title="Équipes" :value="rescue(fn() => \App\Models\Team::count(), 0, false)"
                icon="bi-person-workspace" color="success" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card title="Demandes ouvertes"
                :value="rescue(fn() => \App\Models\InternalRequest::where('status', 'open')->count(), 0, false)"
                icon="bi-chat-left-text" color="warning" />
        </div>
    </div>
@endsection
