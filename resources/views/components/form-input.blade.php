@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false, 'disabled' => false, 'readonly' => false, 'placeholder' => null, 'class' => 'col-md-3'])

<div class="{{ $class }}">
    @if($label)
    <label class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
    />

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
