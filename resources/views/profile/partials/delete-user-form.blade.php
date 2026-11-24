<section>
    <h2 class="h5 fw-bold mb-1">Supprimer mon compte</h2>
    <p class="text-secondary small mb-4">Cette action est définitive : toutes les données associées au compte seront supprimées.</p>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        Supprimer mon compte
    </button>

    <x-ui.modal id="confirmUserDeletion" title="Confirmer la suppression"
        description="Saisissez votre mot de passe pour confirmer la suppression définitive de votre compte."
        :form-action="route('profile.destroy')" method="DELETE" confirm-label="Supprimer définitivement"
        confirm-variant="danger" error-bag="userDeletion" :open-on-error="true">
        <div class="row g-3">
            <x-form.password col="12" field="password" input-id="delete_password" label="Mot de passe"
                error-bag="userDeletion" autocomplete="current-password" />
        </div>
    </x-ui.modal>
</section>
