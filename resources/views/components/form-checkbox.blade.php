@props(['label' => null, 'name', 'value' => '1', 'checked' => false])

<div class="form-check">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        id="checkbox_{{ $name }}"
        class="form-check-input @error($name) is-invalid @enderror"
        @if(old($name, $checked)) checked @endif
    />

    @if($label)
    <label class="form-check-label" for="checkbox_{{ $name }}">
        {{ $label }}
    </label>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
