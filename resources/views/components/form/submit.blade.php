@props(['variant' => 'primary', 'label' => null])

<button type="submit" data-loading-button
    {{ $attributes->merge(['class' => 'btn btn-' . $variant . ' teamo-submit']) }}>
    <span class="spinner-border spinner-border-sm me-2 d-none" data-loading-spinner aria-hidden="true"></span>
    <span data-loading-label>{{ $label ?? $slot }}</span>
</button>
