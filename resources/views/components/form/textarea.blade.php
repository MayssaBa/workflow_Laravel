@props(['col' => 12, 'field', 'label', 'value' => '', 'rows' => 4, 'help' => '', 'required' => false, 'errorBag' => 'default'])

@php
    $describedBy = [];
    $helpId = $field . '-help';
    $errorId = $field . '-error';

    if ($help) {
        $describedBy[] = $helpId;
    }

    if ($errors->{$errorBag}->has($field)) {
        $describedBy[] = $errorId;
    }
@endphp

<div class="col-md-{{ $col }}">
    <label class="form-label fw-semibold" for="{{ $field }}">
        {{ $label }}
        @if ($required)
            <span class="teamo-required" aria-hidden="true">*</span>
        @endif
    </label>

    <textarea id="{{ $field }}" name="{{ $field }}" rows="{{ $rows }}"
        @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        {{ $attributes->merge(['class' => 'form-control' . ($errors->{$errorBag}->has($field) ? ' is-invalid' : '')]) }}
        @required($required)>{{ old($field, $value) }}</textarea>

    @if ($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif

    @error($field, $errorBag)
        <div id="{{ $errorId }}" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
