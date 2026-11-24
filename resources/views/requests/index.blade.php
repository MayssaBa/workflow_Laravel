@extends('layouts.admin.template')

@section('title', 'Demandes internes')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Demandes internes</h1>
            <p class="text-secondary mb-0">
                {{ $pendingCount }} demande(s) ouverte(s) à traiter.
            </p>
        </div>

        @can('create', App\Models\InternalRequest::class)
            <a href="{{ route('requests.create') }}" class="btn btn-primary">
                Nouvelle demande
            </a>
        @endcan
    </div>

    <ul class="nav nav-pills mb-4" aria-label="Type de demandes">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'received' ? 'active' : '' }}"
                href="{{ route('requests.index', ['tab' => 'received', 'status' => $status]) }}"
                @if ($tab === 'received') aria-current="page" @endif>
                Reçues
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'sent' ? 'active' : '' }}"
                href="{{ route('requests.index', ['tab' => 'sent', 'status' => $status]) }}"
                @if ($tab === 'sent') aria-current="page" @endif>
                Envoyées
            </a>
        </li>
    </ul>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('requests.index') }}" class="row g-3 align-items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                @php
                    $statusOptions = collect($statuses)->mapWithKeys(fn ($status) => [
                        $status->value => $status->label(),
                    ])->all();
                @endphp

                <x-form.select field="status" label="Statut" :options="$statusOptions"
                    placeholder="Tous les statuts" :selected="$status" col="4" />

                <div class="col-md-2">
                    <x-form.submit label="Filtrer" variant="outline-primary" />
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-semibold mb-3">Liste des demandes</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Sujet</th>
                            <th scope="col">Expéditeur</th>
                            <th scope="col">Destinataire</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Réponses</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->subject }}</td>
                                <td>{{ App\Models\InternalRequest::participantName($item->sender) }}</td>
                                <td>{{ App\Models\InternalRequest::participantName($item->recipient) }}</td>
                                <td><x-status-badge :status="$item->status" /></td>
                                <td>{{ $item->replies_count }}</td>
                                <td>{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="text-end text-nowrap">
                                    @can('view', $item)
                                        <a href="{{ route('requests.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                            Consulter
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-ui.empty-state icon="bi-chat-left-text" title="Aucune demande"
                                        text="Aucune demande ne correspond à votre sélection.">
                                        @can('create', App\Models\InternalRequest::class)
                                            <x-slot:action>
                                                <a href="{{ route('requests.create') }}" class="btn btn-primary btn-sm">
                                                    Nouvelle demande
                                                </a>
                                            </x-slot:action>
                                        @endcan
                                    </x-ui.empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
