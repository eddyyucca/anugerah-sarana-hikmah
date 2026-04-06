<x-form-row>
    <x-form-input name="technician_code" label="Code" value="{{ old('technician_code', $technician->technician_code ?? '') }}" required class="col-md-3" />
    <x-form-input name="technician_name" label="Name" value="{{ old('technician_name', $technician->technician_name ?? '') }}" required class="col-md-4" />
    <x-form-input name="skill" label="Skill" value="{{ old('skill', $technician->skill ?? '') }}" class="col-md-3" />
    <x-form-input name="phone" label="Phone" value="{{ old('phone', $technician->phone ?? '') }}" class="col-md-2" />
</x-form-row>
