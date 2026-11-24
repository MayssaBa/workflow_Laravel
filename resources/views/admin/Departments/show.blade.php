@extends('layouts.admin.template')

@section('title', 'Détails du département')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Détails du département</h1>
            <p class="text-secondary mb-0">{{ $department->name }}</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary">Modifier</a>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Retour</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Informations du département</div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Nom :</div>
                <div class="col-md-8">{{ $department->name }}</div>
            </div>

            <div class="row">
                <div class="col-md-4 fw-bold">Date de création :</div>
                <div class="col-md-8">{{ $department->created_at?->format('d/m/Y') ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">Équipes du département</div>

        <div class="card-body">
            @if ($department->teams->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Nombre d'employés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department->teams as $team)
                                <tr>
                                    <td class="fw-semibold">{{ $team->name }}</td>
                                    <td>{{ $team->users()->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-secondary mb-0">Aucune équipe n'est affectée à ce département.</p>
            @endif
        </div>
    </div>
@endsection
