<x-form-row>
    <x-form-input name="unit_code" label="Unit Code" value="{{ old('unit_code', $unit->unit_code ?? '') }}" required class="col-md-4" />
    <x-form-input name="unit_model" label="Model" value="{{ old('unit_model', $unit->unit_model ?? '') }}" required class="col-md-4" />
    <x-form-input name="unit_type" label="Type" value="{{ old('unit_type', $unit->unit_type ?? '') }}" class="col-md-4" />
    <x-form-select name="category_id" label="Category" class="col-md-4">
        <option value="">-- Select --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $unit->category_id ?? '')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
        @endforeach
    </x-form-select>
    <x-form-input name="department" label="Department" value="{{ old('department', $unit->department ?? '') }}" class="col-md-4" />
    <x-form-select name="current_status" label="Status" required class="col-md-2">
        @foreach(['available','under_repair','standby'] as $s)
            <option value="{{ $s }}" {{ old('current_status', $unit->current_status ?? 'available')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
        @endforeach
    </x-form-select>
    <x-form-input name="hour_meter" label="Hour Meter" value="{{ old('hour_meter', $unit->hour_meter ?? 0) }}" type="number" step="0.1" class="col-md-2" />
</x-form-row>
