@props(['col' => 12, 'field', 'label', 'type' => 'text', 'value' => '', 'help' => '', 'required' => true])

<div class="col-md-{{ $col }}">
    <label class="form-label fw-semibold" for="{{ $field }}">{{ $label }}</label>
    <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}"
        type="{{ $type }}" @if (! in_array($type, ['password', 'file'], true)) value="{{ old($field, $value) }}" @endif
        {{ $attributes }} @required($required)>
    @if ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
    @error($field)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
