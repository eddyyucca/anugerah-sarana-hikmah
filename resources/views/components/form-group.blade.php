@props(['label' => null, 'name' => null, 'required' => false])

<div class="form-group">
    @if($label)
    <label class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    {{ $slot }}

    @if($name)
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
    @endif
</div>
