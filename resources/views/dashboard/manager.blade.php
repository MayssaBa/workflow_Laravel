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
            <x-stat-card title="Mes projets"
                :value="rescue(fn() => auth()->user()->managedProjects()->count(), 0, false)" icon="bi-folder2-open" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-stat-card title="Tâches créées"
                :value="rescue(fn() => auth()->user()->createdTasks()->count(), 0, false)" icon="bi-check2-square"
                color="success" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-stat-card title="Demandes à traiter"
                :value="rescue(fn() => \App\Models\InternalRequest::where('recipient_id', auth()->id())->where('status', 'open')->count(), 0, false)"
                icon="bi-chat-left-text" color="warning" />
        </div>
    </div>
@endsection
