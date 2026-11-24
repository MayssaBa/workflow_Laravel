@props(['icon' => 'bi-inbox', 'title', 'text'])

<div {{ $attributes->merge(['class' => 'teamo-empty-state text-center p-5']) }}>
    <div class="teamo-empty-icon" aria-hidden="true">
        <i class="bi {{ $icon }}"></i>
    </div>
    <h3 class="h5 fw-bold mt-3 mb-1">{{ $title }}</h3>
    <p class="text-secondary mb-0">{{ $text }}</p>

    @if (isset($action) && trim((string) $action) !== '')
        <div class="mt-3">{{ $action }}</div>
    @endif
</div>
