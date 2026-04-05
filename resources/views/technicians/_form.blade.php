<<<<<<< HEAD
<x-form-row>
    <x-form-input name="technician_code" label="Code" value="{{ old('technician_code', $technician->technician_code ?? '') }}" required class="col-md-3" />
    <x-form-input name="technician_name" label="Name" value="{{ old('technician_name', $technician->technician_name ?? '') }}" required class="col-md-4" />
    <x-form-input name="skill" label="Skill" value="{{ old('skill', $technician->skill ?? '') }}" class="col-md-3" />
    <x-form-input name="phone" label="Phone" value="{{ old('phone', $technician->phone ?? '') }}" class="col-md-2" />
</x-form-row>
=======
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">Code <span class="text-danger">*</span></label><input type="text" name="technician_code" class="form-control @error('technician_code') is-invalid @enderror" value="{{ old('technician_code', $technician->technician_code ?? '') }}" required style="border-radius:10px;">@error('technician_code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="technician_name" class="form-control @error('technician_name') is-invalid @enderror" value="{{ old('technician_name', $technician->technician_name ?? '') }}" required style="border-radius:10px;">@error('technician_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-3"><label class="form-label">Skill</label><input type="text" name="skill" class="form-control" value="{{ old('skill', $technician->skill ?? '') }}" style="border-radius:10px;"></div>
    <div class="col-md-2"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $technician->phone ?? '') }}" style="border-radius:10px;"></div>
</div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
