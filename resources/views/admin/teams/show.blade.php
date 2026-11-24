@extends('layouts.admin.template')

@section('title', 'Détails de l’équipe')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Détails de l’équipe</h1>

            <div>
                <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-primary">
                    Modifier
                </a>

                <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                    Retour
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                Informations de l’équipe
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nom de l’équipe :</div>
                    <div class="col-md-8">{{ $team->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Département :</div>
                    <div class="col-md-8">
                        {{ $team->department?->name ?? 'Non attribué' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 fw-bold">Nombre d’employés :</div>
                    <div class="col-md-8">{{ $team->users->count() }}</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Employés de cette équipe
            </div>

            <div class="card-body">
                @if ($team->users->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($team->users as $employee)
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->phone ?? 'Non renseigné' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-secondary mb-0">
                        Aucun employé n'est affecté à cette équipe.
                    </p>
                @endif
            </div>
        </div>

    </div>
@endsection