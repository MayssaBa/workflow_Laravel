<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Modifier le mot de passe
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Choisissez un mot de passe sécurisé pour protéger votre compte.
        </p>
    </header>

    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Current password -->
        <div>
            <x-input-label for="current_password" value="Mot de passe actuel" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full"
                autocomplete="current-password" required />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <!-- New password -->
        <div>
            <x-input-label for="password" value="Nouveau mot de passe" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm password -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmer le nouveau mot de passe" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer le nouveau mot de passe</x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600">
                    Mot de passe modifié avec succès.
                </p>
            @endif
        </div>
    </form>
</section>