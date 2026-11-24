@extends('layouts.admin.template')

@section('title', 'Nouvelle demande')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Nouvelle demande interne</h1>
            <p class="text-secondary mb-0">Envoyez une demande à un manager ou à l'administrateur.</p>
        </div>

        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
            Retour aux demandes
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('requests.store') }}">
                @csrf

                <div class="row g-3">
                    <x-form.input field="subject" label="Sujet de la demande" :required="true"
                        maxlength="255" placeholder="Exemple : Demande de congé" />

                    <x-form.select field="recipient_id" label="Destinataire" :options="$recipients"
                        placeholder="Choisir un destinataire" :required="true" />

                    <x-form.textarea field="message" label="Message" :required="true" :rows="6"
                        placeholder="Décrivez votre demande..." />

                    <div class="col-12 d-flex gap-2">
                        <x-form.submit label="Envoyer la demande" />

                        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
