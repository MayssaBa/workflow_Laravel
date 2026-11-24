@props(['col' => 12, 'field', 'label', 'type' => 'text', 'value' => '', 'help' => '', 'required' => true, 'errorBag' => 'default', 'inputId' => null])

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

    <input id="{{ $inputId }}" name="{{ $field }}" type="{{ $type }}"
        @if (! in_array($type, ['password', 'file'], true)) value="{{ old($field, $value) }}" @endif
        @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        {{ $attributes->merge(['class' => 'form-control' . ($errors->{$errorBag}->has($field) ? ' is-invalid' : '')]) }}
        @required($required)>

    @if ($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif

    @error($field, $errorBag)
        <div id="{{ $errorId }}" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
