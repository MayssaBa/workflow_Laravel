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
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('tasks.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Mes tâches"
                    :value="rescue(fn() => auth()->user()->assignedTasks()->count(), 0, false)" icon="bi-check2-square" />
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('projects.index') }}" class="text-decoration-none text-reset d-block">
                <x-stat-card title="Mes projets"
                    :value="rescue(fn() => auth()->user()->projects()->count(), 0, false)" icon="bi-folder2-open"
                    color="info" />
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-stat-card title="Demandes envoyées"
                :value="rescue(fn() => \App\Models\InternalRequest::where('sender_id', auth()->id())->count(), 0, false)"
                icon="bi-send" color="warning" />
        </div>
    </div>
@endsection
