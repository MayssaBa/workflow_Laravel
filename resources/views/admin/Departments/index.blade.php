<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des départements
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Titre et bouton Ajouter -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Gestion des départements
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Gérer les départements de votre organisation.
                    </p>
                </div>

                <a href="{{ route('admin.departments.create') }}"
                    class="inline-flex items-center px-5 py-3 bg-indigo-600 text-black font-semibold rounded-lg shadow hover:bg-indigo-700">
                    + Ajouter un département
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

            <!-- Tableau -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Liste des départements
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Nom du département
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Nombre d'équipes
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Date de création
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse ($departments as $department)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                                            {{ $department->name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $department->teams_count }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            {{ $department->created_at->format('d/m/Y') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-3">

                                                <a href="{{ route('admin.departments.show', $department) }}"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    Voir
                                                </a>

                                                <a href="{{ route('admin.departments.edit', $department) }}"
                                                    class="text-indigo-600 hover:text-indigo-800">
                                                    Modifier
                                                </a>

                                                <form action="{{ route('admin.departments.destroy', $department) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Voulez-vous vraiment supprimer ce département ?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        Supprimer
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Aucun département trouvé.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $departments->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>