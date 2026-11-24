<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Détails de l'employé
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- En-tête et boutons --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $employee->name }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Informations détaillées de l'employé.
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.employees.edit', $employee) }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Modifier
                    </a>

                    <a href="{{ route('admin.employees.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Retour
                    </a>
                </div>
            </div>

            {{-- Informations --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-5">
                        Informations personnelles
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">Nom complet</p>
                            <p class="font-medium text-gray-900">
                                {{ $employee->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Adresse email</p>
                            <p class="font-medium text-gray-900">
                                {{ $employee->email }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="font-medium text-gray-900">
                                {{ $employee->phone ?? 'Non renseigné' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Rôle</p>
                            <p class="font-medium text-gray-900">
                                {{ ucfirst($employee->role) }}
                            </p>
                        </div>

                    </div>

                    <hr class="my-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-5">
                        Affectation organisationnelle
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">Département</p>
                            <p class="font-medium text-gray-900">
                                {{ $employee->team->department->name ?? 'Non affecté' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Équipe</p>
                            <p class="font-medium text-gray-900">
                                {{ $employee->team->name ?? 'Non affecté' }}
                            </p>
                        </div>

                    </div>

                    <hr class="my-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-5">
                        Statut du compte
                    </h3>

                    @if ($employee->is_active)
                        <span class="px-3 py-2 text-sm rounded-full bg-green-100 text-green-800">
                            Compte actif
                        </span>
                    @else
                        <span class="px-3 py-2 text-sm rounded-full bg-red-100 text-red-800">
                            Compte désactivé
                        </span>
                    @endif

                    <div class="mt-6 text-sm text-gray-500">
                        <p>
                            Créé le :
                            {{ $employee->created_at?->format('d/m/Y à H:i') }}
                        </p>

                        <p>
                            Dernière modification :
                            {{ $employee->updated_at?->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>