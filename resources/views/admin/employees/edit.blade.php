@extends('layouts.admin.template')

@section('title', 'Modifier un employé')

@section('content')
    @php
        $teamOptions = $teams->mapWithKeys(function ($team) {
            $label = $team->name . ($team->department ? ' — ' . $team->department->name : '');

            return [$team->id => $label];
        })->all();
    @endphp

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Modifier l'employé</h1>
            <p class="text-secondary mb-0">Modifiez les informations de {{ $employee->name }}.</p>
        </div>

        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" data-loading>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <x-form.input col="6" field="name" label="Nom complet"
                        :value="old('name', $employee->name)" autocomplete="name" />
                    <x-form.input col="6" field="email" type="email" label="Adresse e-mail"
                        :value="old('email', $employee->email)" autocomplete="email" />
                    <x-form.input col="6" field="phone" label="Téléphone"
                        :value="old('phone', $employee->phone)" :required="false" autocomplete="tel"
                        help="Exemple : +216 XX XXX XXX" />
                    <x-form.select col="6" field="team_id" label="Équipe" :options="$teamOptions"
                        placeholder="Aucune équipe" :selected="old('team_id', $employee->team_id)"
                        :required="false" />
                    <x-form.password col="6" field="password" label="Nouveau mot de passe"
                        autocomplete="new-password" :required="false"
                        help="Laissez ce champ vide si vous ne souhaitez pas modifier le mot de passe." />
                    <x-form.password col="6" field="password_confirmation" label="Confirmer le nouveau mot de passe"
                        autocomplete="new-password" :required="false" />
                </div>

                <div class="d-flex align-items-center gap-3 mt-4">
                    <x-form.submit label="Enregistrer les modifications" />
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
