@props(['label' => null, 'name', 'value' => null, 'required' => false, 'disabled' => false, 'readonly' => false, 'class' => 'col-md-3'])

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
        type="date"
        name="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror"
        value="{{ old($name, $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '') }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
    />

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
