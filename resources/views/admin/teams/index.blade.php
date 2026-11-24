<x-app-layout>
    @section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestion des équipes</h1>

            <a href="{{ route('admin.teams.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-black font-semibold py-2 px-4 rounded">
                Ajouter une équipe
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nom de l'équipe</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Département</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre d'employés</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($teams as $team)
                        <tr>
                            <td class="px-6 py-4 text-gray-800">
                                {{ $team->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $team->department?->name ?? 'Non attribué' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $team->users_count }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.teams.show', $team) }}" class="text-blue-600 hover:underline">
                                        Voir
                                    </a>

                                    <a href="{{ route('admin.teams.edit', $team) }}"
                                        class="text-indigo-600 hover:underline">
                                        Modifier
                                    </a>

                                    <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer cette équipe ?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 hover:underline">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Aucune équipe trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $teams->links() }}
        </div>

    </div>
</x-app-layout>