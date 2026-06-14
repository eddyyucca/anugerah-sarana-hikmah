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
        @if($slot->isEmpty())
            {{-- Props-based options --}}
            <option value="">{{ $placeholder }}</option>
            @if(is_array($options) || $options instanceof Illuminate\Support\Collection)
                @foreach($options as $optValue => $optLabel)
                    @if(is_array($optLabel))
                        <optgroup label="{{ $optValue }}">
                            @foreach($optLabel as $val => $lbl)
                                <option value="{{ $val }}" @if(old($name, $value) == $val) selected @endif>{{ $lbl }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $optValue }}" @if(old($name, $value) == $optValue) selected @endif>{{ $optLabel }}</option>
                    @endif
                @endforeach
            @endif
        @else
            {{-- Slot-based options (children passed as <option> tags) --}}
            {{ $slot }}
        @endif
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
