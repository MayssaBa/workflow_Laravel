<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des employés
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Titre et bouton Ajouter -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Gestion des employés
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Gérer les employés de votre organisation.
                    </p>
                </div>

                <a href="{{ route('admin.employees.create') }}"
                    class="inline-flex items-center px-5 py-3 bg-indigo-600 text-black font-semibold rounded-lg shadow hover:bg-indigo-700">
                    + Ajouter un employé
                </a>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Liste des employés -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Liste des employés
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Nom
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Email
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Téléphone
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Équipe
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Département
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Statut
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $employee->name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $employee->email }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $employee->phone ?? 'Non renseigné' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $employee->team->name ?? 'Aucune équipe' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $employee->team->department->name ?? 'Aucun département' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($employee->is_active)
                                                <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-full">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-full">
                                                    Désactivé
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-2">

                                                <a href="{{ route('admin.employees.show', $employee) }}"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    Voir
                                                </a>

                                                <a href="{{ route('admin.employees.edit', $employee) }}"
                                                    class="text-indigo-600 hover:text-indigo-800">
                                                    Modifier
                                                </a>

                                                @if ($employee->is_active)
                                                    <form action="{{ route('admin.employees.destroy', $employee) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Voulez-vous vraiment désactiver cet employé ?');">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                                            Désactiver
                                                        </button>
                                                    </form>

                                                @else
                                                    <form
                                                        action="{{ route('admin.employees.activate', ['employee' => $employee->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Voulez-vous vraiment réactiver cet employé ?');">

                                                        @csrf

                                                        <button type="submit" class="text-green-600 hover:text-green-800">
                                                            Réactiver
                                                        </button>
                                                    </form>
                                                @endif


                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Aucun employé trouvé.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $employees->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>