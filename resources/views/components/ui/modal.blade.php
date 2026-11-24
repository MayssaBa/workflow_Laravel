@props(['id', 'title', 'description' => null, 'size' => 'md', 'formAction' => null, 'method' => 'POST', 'confirmLabel' => 'Confirmer', 'cancelLabel' => 'Annuler', 'confirmVariant' => 'primary', 'errorBag' => null, 'openOnError' => false])

@php
    $hasErrors = $openOnError && $errorBag && $errors->{$errorBag}->isNotEmpty();
    $method = strtoupper($method);
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true"
    data-teamo-modal @if ($hasErrors) data-teamo-has-errors="true" @endif>
    <div class="modal-dialog modal-{{ $size }} modal-dialog-centered">
        @if ($formAction)
            <form method="POST" action="{{ $formAction }}" data-loading>
                @csrf
                @if (! in_array($method, ['POST', 'GET'], true))
                    @method($method)
                @endif
        @endif

        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="{{ $id }}Label">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                @if ($description)
                    <p class="text-secondary" id="{{ $id }}Description">{{ $description }}</p>
                @endif

                {{ $slot }}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ $cancelLabel }}</button>
                <button type="{{ $formAction ? 'submit' : 'button' }}" class="btn btn-{{ $confirmVariant }} teamo-submit"
                    @if (! $formAction) data-bs-dismiss="modal" @endif data-loading-button>
                    <span class="spinner-border spinner-border-sm me-2 d-none" data-loading-spinner aria-hidden="true"></span>
                    <span data-loading-label>{{ $confirmLabel }}</span>
                </button>
            </div>
        </div>

        @if ($formAction)
            </form>
        @endif
    </div>
</div>
