@extends('layouts.admin.template')

@section('title', 'Modifier un employé')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Modifier l'employé
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Modifiez les informations de {{ $employee->name }}.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                        <p class="font-semibold mb-2">
                            Veuillez corriger les erreurs suivantes :
                        </p>

                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.employees.update', $employee) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <!-- Nom -->
                    <div class="mb-4">
                        <label for="name"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $employee->name) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse e-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $employee->email) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Téléphone -->
                    <div class="mb-4">
                        <label for="phone"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de téléphone
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $employee->phone) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Équipe -->
                    <div class="mb-4">
                        <label for="team_id"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Équipe / Département
                        </label>

                        <select
                            id="team_id"
                            name="team_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Aucune équipe</option>

                            @foreach ($teams as $team)
                                <option
                                    value="{{ $team->id }}"
                                    {{ old('team_id', $employee->team_id) == $team->id ? 'selected' : '' }}
                                >
                                    {{ $team->name }}
                                    @if ($team->department)
                                        — {{ $team->department->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nouveau mot de passe -->
                    <div class="mb-4">
                        <label for="password"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p class="text-xs text-gray-500 mt-1">
                            Laissez ce champ vide si vous ne souhaitez pas modifier le mot de passe.
                        </p>
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div class="mb-6">
                        <label for="password_confirmation"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le nouveau mot de passe
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-between">

                        <a href="{{ route('admin.employees.index') }}"
                        class="px-5 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-3 bg-indigo-600 text-black font-semibold rounded-lg shadow hover:bg-indigo-700"
                        >
                            Enregistrer les modifications
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
