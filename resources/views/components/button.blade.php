@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'icon' => null, 'href' => null, 'disabled' => false, 'class' => null])

@php
    $baseClasses = 'btn';
    $variantClasses = match($variant) {
        'primary' => 'btn-primary btn-primary-rounded',
        'danger' => 'btn-danger btn-primary-rounded',
        'secondary' => 'btn-secondary btn-secondary-rounded',
        'light' => 'btn-light',
        'success' => 'btn-success btn-primary-rounded',
        'warning' => 'btn-warning btn-primary-rounded',
        'info' => 'btn-info btn-primary-rounded',
        default => 'btn-primary btn-primary-rounded'
    };

    $sizeClasses = match($size) {
        'sm' => 'btn-sm btn-action-sm',
        'lg' => 'btn-lg',
        default => ''
    };

    $finalClass = "{$baseClasses} {$variantClasses} {$sizeClasses} {$class}";
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $finalClass }}" @if($disabled) onclick="return false;" style="opacity:0.6;cursor:not-allowed;" @endif>
        @if($icon)
            <i class="{{ $icon }} me-1"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $finalClass]) }} @if($disabled) disabled @endif>
        @if($icon)
            <i class="{{ $icon }} me-1"></i>
        @endif
        {{ $slot }}
    </button>
@endif
