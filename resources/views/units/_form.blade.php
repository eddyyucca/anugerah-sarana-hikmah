<<<<<<< HEAD
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
=======
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Unit Code <span class="text-danger">*</span></label>
        <input type="text" name="unit_code" class="form-control @error('unit_code') is-invalid @enderror"
               value="{{ old('unit_code', $unit->unit_code ?? '') }}" required style="border-radius:10px;">
        @error('unit_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Model <span class="text-danger">*</span></label>
        <input type="text" name="unit_model" class="form-control @error('unit_model') is-invalid @enderror"
               value="{{ old('unit_model', $unit->unit_model ?? '') }}" required style="border-radius:10px;">
        @error('unit_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Type</label>
        <input type="text" name="unit_type" class="form-control" value="{{ old('unit_type', $unit->unit_type ?? '') }}" style="border-radius:10px;">
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" style="border-radius:10px;">
            <option value="">-- Select --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $unit->category_id ?? '')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Department</label>
        <input type="text" name="department" class="form-control" value="{{ old('department', $unit->department ?? '') }}" style="border-radius:10px;">
    </div>
    <div class="col-md-2">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="current_status" class="form-select" required style="border-radius:10px;">
            @foreach(['available','under_repair','standby'] as $s)
                <option value="{{ $s }}" {{ old('current_status', $unit->current_status ?? 'available')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Hour Meter</label>
        <input type="number" step="0.1" name="hour_meter" class="form-control" value="{{ old('hour_meter', $unit->hour_meter ?? 0) }}" style="border-radius:10px;">
    </div>
</div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
