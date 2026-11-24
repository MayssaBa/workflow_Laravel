<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajouter un département
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Nouveau département
                </h1>

                <p class="text-sm text-gray-500 mb-6">
                    Remplissez le formulaire pour créer un département.
                </p>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.departments.store') }}" method="POST">

                    @csrf

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du département
                        </label>

                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Exemple : Ressources humaines" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex justify-between items-center">

                        <a href="{{ route('admin.departments.index') }}"
                            class="px-5 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
                            Annuler
                        </a>

                        <button type="submit" style="background-color: #4f46e5; color: white;"
                            class="px-5 py-3 font-semibold rounded-lg shadow hover:opacity-90">
                            Enregistrer le département
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>