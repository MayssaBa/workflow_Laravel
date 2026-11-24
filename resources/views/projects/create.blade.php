@extends('layouts.admin.template')

@section('title', 'Nouveau projet')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Nouveau projet</h1>
            <p class="text-secondary mb-0">Renseignez les informations du projet.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-4">Informations du projet</h2>

            @include('projects._form', [
                'project' => null,
                'action' => route('projects.store'),
                'method' => 'POST',
                'submitLabel' => 'Créer le projet',
            ])
        </div>
    </div>
@endsection
