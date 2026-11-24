<section>
    <h2 class="h5 fw-bold mb-1">Compte</h2>
    <p class="text-secondary small mb-4">Ces informations sont gérées par un administrateur.</p>

    <dl class="row mb-0">
        <dt class="col-sm-4 col-lg-3">Rôle</dt>
        <dd class="col-sm-8 col-lg-9"><x-status-badge :status="$user->role" /></dd>

        <dt class="col-sm-4 col-lg-3">Département</dt>
        <dd class="col-sm-8 col-lg-9">{{ $user->team?->department?->name ?? 'Non affecté' }}</dd>

        <dt class="col-sm-4 col-lg-3">Équipe</dt>
        <dd class="col-sm-8 col-lg-9">{{ $user->team?->name ?? 'Non affecté' }}</dd>

        <dt class="col-sm-4 col-lg-3">Date de création</dt>
        <dd class="col-sm-8 col-lg-9">{{ $user->created_at?->format('d/m/Y') }}</dd>

        <dt class="col-sm-4 col-lg-3">Statut</dt>
        <dd class="col-sm-8 col-lg-9">
            <x-status-badge :status="$user->is_active ? 'actif' : 'inactif'" />
        </dd>
    </dl>
</section>
