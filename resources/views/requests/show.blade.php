@extends('layouts.admin.template')

@section('title', $internalRequest->subject)

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $internalRequest->subject }}</h1>
            <p class="text-secondary mb-0">
                Demande du {{ $internalRequest->created_at?->format('d/m/Y H:i') ?? '—' }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
                Retour aux demandes
            </a>

            @can('close', $internalRequest)
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#close-request-modal">
                    Clôturer
                </button>
            @endcan
        </div>
    </div>

    @can('close', $internalRequest)
        <x-ui.modal id="close-request-modal" title="Clôturer la demande ?"
            description="La demande sera clôturée et aucune nouvelle réponse ne pourra être ajoutée."
            formAction="{{ route('requests.close', $internalRequest) }}" method="PATCH"
            confirmLabel="Clôturer" confirmVariant="danger" />
    @endcan

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="text-secondary small">Expéditeur</div>
                    <div class="fw-semibold">
                        {{ App\Models\InternalRequest::participantName($internalRequest->sender) }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-secondary small">Destinataire</div>
                    <div class="fw-semibold">
                        {{ App\Models\InternalRequest::participantName($internalRequest->recipient) }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-secondary small">Statut</div>
                    <div><x-status-badge :status="$internalRequest->status" /></div>
                </div>
            </div>

            <h2 class="h5 fw-semibold mb-2">Message</h2>
            <p class="mb-0">{!! nl2br(e($internalRequest->message)) !!}</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Fil de réponses</h2>

            @forelse ($internalRequest->replies as $reply)
                <div class="border-top py-3">
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-2">
                        <div class="fw-semibold">
                            {{ App\Models\InternalRequest::participantName($reply->user) }}
                        </div>
                        <div class="text-secondary small">
                            {{ $reply->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </div>
                    </div>
                    <p class="mb-0">{!! nl2br(e($reply->body)) !!}</p>
                </div>
            @empty
                <x-ui.empty-state icon="bi-chat-left-text" title="Aucune réponse"
                    text="Aucune réponse n'a encore été ajoutée à cette demande." />
            @endforelse
        </div>
    </div>

    @can('reply', $internalRequest)
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-3">Ajouter une réponse</h2>

                <form method="POST" action="{{ route('requests.reply', $internalRequest) }}">
                    @csrf

                    <div class="row g-3">
                        <x-form.textarea field="body" label="Votre réponse" :required="true" :rows="4"
                            placeholder="Écrivez votre réponse..." />

                        <div class="col-12">
                            <x-form.submit label="Envoyer la réponse" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endsection
