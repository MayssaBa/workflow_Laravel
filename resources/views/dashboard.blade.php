@extends('layouts.admin.template')

@section('title', 'Tableau de bord')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Tableau de bord</h1>
            <p class="text-secondary mb-0">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-0">Vous êtes connecté à Teamo.</p>
        </div>
    </div>
@endsection
