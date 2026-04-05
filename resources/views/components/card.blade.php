@props(['title' => null, 'subtitle' => null, 'icon' => null, 'class' => null])

<div class="erp-card {{ $class }}">
    @if($title || $subtitle || $icon || isset($header))
    <div class="erp-card-header">
        @if(isset($header))
            {{ $header }}
        @else
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    @if($title)
                        <h6 class="section-title mb-1">
                            @if($icon)
                                <i class="{{ $icon }} me-2"></i>
                            @endif
                            {{ $title }}
                        </h6>
                    @endif
                    @if($subtitle)
                        <p class="section-subtitle mb-0">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
    @endif

    <div class="erp-card-body">
        {{ $slot }}
    </div>
</div>
