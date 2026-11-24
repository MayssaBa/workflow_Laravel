@props(['type' => 'success', 'icon' => null])

@php
    $icons = [
        'success' => 'bi-check-circle',
        'info' => 'bi-info-circle',
        'warning' => 'bi-exclamation-triangle',
        'danger' => 'bi-x-octagon',
    ];
    $icon = $icon ?? ($icons[$type] ?? 'bi-info-circle');
@endphp

<div role="alert" aria-live="polite"
    {{ $attributes->merge(['class' => "alert alert-$type alert-dismissible fade show shadow-sm"]) }}>
    <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>
    <span>{{ $slot }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
</div>
