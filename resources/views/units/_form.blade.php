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
