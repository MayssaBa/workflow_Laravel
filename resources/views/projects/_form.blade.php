<form method="POST" action="{{ $action }}" data-loading>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <x-form.input col="12" field="name" label="Nom du projet" :value="$project?->name" required />
        <x-form.textarea col="12" field="description" label="Description" :value="$project?->description" />
        <x-form.input col="6" field="start_date" label="Date de début" type="date"
            :value="$project?->start_date?->toDateString()" :required="false" />
        <x-form.input col="6" field="end_date" label="Date de fin" type="date"
            :value="$project?->end_date?->toDateString()" :required="false"
            help="La date de fin doit être postérieure à la date de début." />
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
        <x-form.submit :label="$submitLabel" />
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
