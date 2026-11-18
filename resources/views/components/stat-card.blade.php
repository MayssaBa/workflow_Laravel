@props(['title', 'value', 'color' => 'primary', 'icon' => 'bi-bar-chart', 'hint' => null])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm h-100']) }}>
    <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-{{ $color }}-subtle text-{{ $color }} d-flex align-items-center justify-content-center fs-4"
            style="width: 52px; height: 52px;" aria-hidden="true">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div>
            <div class="text-secondary small">{{ $title }}</div>
            <div class="h3 fw-bold mb-0">{{ $value }}</div>
            @if ($hint)
                <div class="small text-secondary">{{ $hint }}</div>
            @endif
        </div>
    </div>
</div>
