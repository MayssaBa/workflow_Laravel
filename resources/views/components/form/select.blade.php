@props(['col' => 12, 'field', 'label', 'options' => [], 'selected' => null, 'placeholder' => null, 'help' => '', 'required' => false, 'errorBag' => 'default'])

@php
    $describedBy = [];
    $helpId = $field . '-help';
    $errorId = $field . '-error';
    $oldValue = old($field, $selected);

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

    <select id="{{ $field }}" name="{{ $field }}"
        @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        {{ $attributes->merge(['class' => 'form-select' . ($errors->{$errorBag}->has($field) ? ' is-invalid' : '')]) }}
        @required($required)>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected((string) $oldValue === (string) $value)>{{ $optionLabel }}</option>
        @endforeach

        {{ $slot }}
    </select>

    @if ($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif

    @error($field, $errorBag)
        <div id="{{ $errorId }}" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
