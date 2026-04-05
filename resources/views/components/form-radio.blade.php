@props(['label' => null, 'name', 'options' => [], 'value' => null, 'required' => false])

<div class="form-group">
    @if($label)
    <label class="d-block mb-2">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    @foreach($options as $optValue => $optLabel)
    <div class="form-check">
        <input
            type="radio"
            name="{{ $name }}"
            value="{{ $optValue }}"
            id="radio_{{ $name }}_{{ $optValue }}"
            class="form-check-input @error($name) is-invalid @enderror"
            @if(old($name, $value) == $optValue) checked @endif
            @if($required) required @endif
        />
        <label class="form-check-label" for="radio_{{ $name }}_{{ $optValue }}">
            {{ $optLabel }}
        </label>
    </div>
    @endforeach

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
