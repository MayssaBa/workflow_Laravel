@props(['action', 'message' => 'Voulez-vous vraiment désactiver cet employé ?', 'label' => 'Désactiver', 'title' => 'Confirmer la désactivation', 'confirmLabel' => 'Désactiver', 'method' => 'DELETE', 'errorBag' => null])

@php
    $modalId = 'confirm-' . md5($action);
@endphp

<button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
    aria-label="{{ $label }}">
    {{ $label }}
</button>

<x-ui.modal :id="$modalId" :title="$title" :description="$message" size="md" :form-action="$action"
    :method="$method" :confirm-label="$confirmLabel" confirm-variant="danger" :error-bag="$errorBag" :open-on-error="true" />
