@props(['label' => null, 'name', 'value' => null, 'required' => false, 'disabled' => false, 'readonly' => false, 'rows' => 3, 'placeholder' => null, 'class' => 'col-md-6'])

<div class="{{ $class }}">
    @if($label)
    <label class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    <textarea
        name="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror"
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
