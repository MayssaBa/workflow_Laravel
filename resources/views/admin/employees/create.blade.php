@extends('layouts.admin.template')

@section('title', 'Ajouter un employé')

@section('content')
    @php
        $teamOptions = $teams->mapWithKeys(function ($team) {
            $label = $team->name . ($team->department ? ' — ' . $team->department->name : '');

            return [$team->id => $label];
        })->all();
    @endphp

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Ajouter un employé</h1>
            <p class="text-secondary mb-0">Créer un nouveau compte employé.</p>
        </div>

        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.employees.store') }}" method="POST" data-loading>
                @csrf

                <div class="row g-3">
                    <x-form.input col="6" field="name" label="Nom complet" :value="old('name')" autocomplete="name" />
                    <x-form.input col="6" field="email" type="email" label="Adresse e-mail" :value="old('email')"
                        autocomplete="email" />
                    <x-form.input col="6" field="phone" label="Téléphone" :value="old('phone')" :required="false"
                        autocomplete="tel" help="Exemple : +216 XX XXX XXX" />
                    <x-form.select col="6" field="team_id" label="Équipe" :options="$teamOptions"
                        placeholder="Aucune équipe" :required="false" />
                    <x-form.password col="6" field="password" label="Mot de passe" autocomplete="new-password"
                        help="8 caractères minimum." />
                    <x-form.password col="6" field="password_confirmation" label="Confirmer le mot de passe"
                        autocomplete="new-password" />
                </div>

                <div class="d-flex align-items-center gap-3 mt-4">
                    <x-form.submit label="Enregistrer l'employé" />
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
