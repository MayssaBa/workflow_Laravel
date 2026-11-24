<section>
    <h2 class="h5 fw-bold mb-1">Informations personnelles</h2>
    <p class="text-secondary small mb-4">Modifiez votre nom, votre adresse e-mail, votre téléphone et votre photo.</p>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" data-loading
        data-unsaved-warning>
        @csrf
        @method('PATCH')

        <div class="row g-3">
            <x-form.input col="6" field="name" label="Nom complet" :value="old('name', $user->name)" autocomplete="name" />
            <x-form.input col="6" field="email" type="email" label="Adresse e-mail" :value="old('email', $user->email)"
                autocomplete="username" />
            <x-form.input col="6" field="phone" label="Téléphone" :value="old('phone', $user->phone)" :required="false"
                help="Exemple : +216 XX XXX XXX" autocomplete="tel" />

            <div class="col-12">
                <x-form.input field="avatar" type="file" label="Photo de profil" :required="false"
                    help="JPG, PNG ou WebP, 2 Mo maximum." accept="image/jpeg,image/png,image/webp"
                    data-avatar-preview-input />

                <div class="teamo-avatar-preview mt-3" data-avatar-preview-wrap @if (! $user->avatar_url) hidden @endif>
                    <img data-avatar-preview src="{{ $user->avatar_url ?? '' }}" alt="Aperçu de la photo de profil">
                </div>

                @if ($user->avatar_path)
                    <button type="button" class="btn btn-outline-danger btn-sm mt-3" data-bs-toggle="modal"
                        data-bs-target="#confirmAvatarDeletion">
                        Supprimer la photo
                    </button>

                    <x-ui.modal id="confirmAvatarDeletion" title="Supprimer la photo de profil"
                        description="Votre photo actuelle sera supprimée définitivement."
                        :form-action="route('profile.avatar.destroy')" method="DELETE" confirm-label="Supprimer la photo"
                        confirm-variant="danger" />
                @endif
            </div>
        </div>

        <p class="text-warning small mt-3 mb-0">
            Si vous modifiez votre adresse e-mail, sa vérification devra être refaite.
        </p>

        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <x-form.submit label="Enregistrer" />
            <button type="button" class="btn btn-outline-secondary" data-unsaved-reset>Annuler</button>
        </div>
    </form>
</section>
