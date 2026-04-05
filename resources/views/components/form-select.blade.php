@props(['label' => null, 'name', 'options' => [], 'value' => null, 'required' => false, 'disabled' => false, 'searchable' => true, 'placeholder' => '-- Select --', 'class' => 'col-md-3'])

<div class="{{ $class }}">
    @if($label)
    <label class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    <select
        name="{{ $name }}"
        class="form-select tom-select @error($name) is-invalid @enderror"
        @if($required) required @endif
        @if($disabled) disabled @endif
    >
        <option value="">{{ $placeholder }}</option>

        @if(is_array($options) || $options instanceof Illuminate\Support\Collection)
            @foreach($options as $optValue => $optLabel)
                @if(is_array($optLabel))
                    {{-- Optgroup support --}}
                    <optgroup label="{{ $optValue }}">
                        @foreach($optLabel as $val => $label)
                            <option value="{{ $val }}" @if(old($name, $value) == $val) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $optValue }}" @if(old($name, $value) == $optValue) selected @endif>
                        {{ $optLabel }}
                    </option>
                @endif
            @endforeach
        @endif
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
