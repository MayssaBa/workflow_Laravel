<section>
    <h2 class="h5 fw-bold mb-1">Sécurité</h2>
    <p class="text-secondary small mb-4">Choisissez un mot de passe sécurisé pour protéger votre compte.</p>

    <form method="POST" action="{{ route('profile.password.update') }}" data-loading data-unsaved-warning>
        @csrf
        @method('PUT')

        <div class="row g-3">
            <x-form.password col="6" field="current_password" label="Mot de passe actuel"
                autocomplete="current-password" />
            <x-form.password col="6" field="password" label="Nouveau mot de passe" autocomplete="new-password"
                help="Au moins 8 caractères, une majuscule, un chiffre et un symbole." data-password-strength-input />

            <div class="col-12">
                <div class="teamo-password-strength" data-password-strength data-level="0" aria-live="polite">
                    <div class="d-flex gap-1" aria-hidden="true">
                        <span data-strength-segment></span>
                        <span data-strength-segment></span>
                        <span data-strength-segment></span>
                        <span data-strength-segment></span>
                    </div>
                    <div class="d-flex flex-wrap column-gap-3 row-gap-1 mt-2 small text-secondary">
                        <span data-strength-rule="length">8 caractères minimum</span>
                        <span data-strength-rule="uppercase">Une majuscule</span>
                        <span data-strength-rule="digit">Un chiffre</span>
                        <span data-strength-rule="symbol">Un symbole</span>
                    </div>
                    <span class="visually-hidden" data-strength-label>Force du mot de passe</span>
                </div>
            </div>

            <x-form.password col="6" field="password_confirmation" label="Confirmer le nouveau mot de passe"
                autocomplete="new-password" help="La confirmation doit correspondre au mot de passe."
                data-password-confirmation-input data-password-confirmation-target="password"
                data-password-confirmation-feedback="password_confirmation-help" />
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <x-form.submit label="Enregistrer le nouveau mot de passe" />
            <button type="button" class="btn btn-outline-secondary" data-unsaved-reset>Annuler</button>
        </div>
    </form>
</section>
