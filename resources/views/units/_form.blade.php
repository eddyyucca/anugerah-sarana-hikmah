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
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Budget Perbaikan / Bulan <span class="text-muted" style="font-size:.82rem;">(opsional)</span></label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius:10px 0 0 10px;">IDR</span>
                <input type="number" step="1000" min="0" name="monthly_budget_limit"
                    class="form-control @error('monthly_budget_limit') is-invalid @enderror"
                    style="border-radius:0 10px 10px 0;"
                    value="{{ old('monthly_budget_limit', $unit->monthly_budget_limit ?? '') }}"
                    placeholder="Kosongkan jika tidak ada batas">
                @error('monthly_budget_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-text">Jika biaya perbaikan unit melebihi batas ini dalam 1 bulan, WO baru membutuhkan persetujuan level tertinggi.</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Budget Jarak / Bulan <span class="text-muted" style="font-size:.82rem;">(opsional)</span></label>
            <div class="input-group">
                <input type="number" step="100" min="0" name="monthly_km_budget"
                    class="form-control @error('monthly_km_budget') is-invalid @enderror"
                    style="border-radius:10px 0 0 10px;"
                    value="{{ old('monthly_km_budget', $unit->monthly_km_budget ?? '') }}"
                    placeholder="Kosongkan jika tidak ada batas">
                <span class="input-group-text" style="border-radius:0 10px 10px 0;">km</span>
                @error('monthly_km_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-text">Batas odometer km per bulan. Alert muncul jika unit melampaui jarak ini.</div>
        </div>
    </div>
</x-form-row>
