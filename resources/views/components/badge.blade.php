@props(['variant' => 'secondary', 'icon' => null, 'text' => null])

@php
    $badgeClass = match($variant) {
        'success' => 'badge badge-soft-success',
        'danger' => 'badge badge-soft-danger',
        'warning' => 'badge badge-soft-warning',
        'info' => 'badge badge-soft-info',
        default => 'badge badge-secondary'
    };
@endphp

<span class="{{ $badgeClass }}">
    @if($icon)
        <i class="{{ $icon }} me-1"></i>
    @endif
    {{ $text ?? $slot }}
</span>
