@props(['col' => 12, 'field', 'label', 'help' => '', 'required' => true, 'errorBag' => 'default', 'inputId' => null])

@php
    $inputId = $inputId ?? $field;
    $describedBy = [];
    $helpId = $inputId . '-help';
    $errorId = $inputId . '-error';

    if ($help) {
        $describedBy[] = $helpId;
    }

    if ($errors->{$errorBag}->has($field)) {
        $describedBy[] = $errorId;
    }
@endphp

<div class="col-md-{{ $col }}">
    <label class="form-label fw-semibold" for="{{ $inputId }}">
        {{ $label }}
        @if ($required)
            <span class="teamo-required" aria-hidden="true">*</span>
        @endif
    </label>

    <div class="teamo-password-group">
        <input id="{{ $inputId }}" name="{{ $field }}" type="password"
            @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
            {{ $attributes->merge(['class' => 'form-control pe-5' . ($errors->{$errorBag}->has($field) ? ' is-invalid' : '')]) }}
            @required($required)>

        <button type="button" class="btn btn-sm btn-link text-secondary teamo-password-toggle" data-password-toggle="{{ $inputId }}"
            aria-label="Afficher le mot de passe" aria-pressed="false" aria-controls="{{ $inputId }}">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
    </div>

    @if ($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif

    @error($field, $errorBag)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
