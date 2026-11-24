@extends('layouts.admin.template')

@section('title', 'Modifier une équipe')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Modifier une équipe</h1>

            <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                Retour
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <form action="{{ route('admin.teams.update', $team) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de l'équipe</label>

                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $team->name) }}" required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Département</label>

                        <select name="department_id" id="department_id"
                            class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner un département --</option>

                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id', $team->department_id) == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer les modifications
                    </button>

                    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>

                </form>

            </div>
        </div>

    </div>
@endsection