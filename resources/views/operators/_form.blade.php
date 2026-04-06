<x-form-row>
    <x-form-input name="operator_code" label="Operator Code" value="{{ $operator->operator_code ?? '' }}" required class="col-md-3" />
    <x-form-input name="operator_name" label="Name" value="{{ $operator->operator_name ?? '' }}" required class="col-md-4" />
    <x-form-input name="nik" label="NIK" value="{{ $operator->nik ?? '' }}" class="col-md-3" />
    <x-form-input name="phone" label="Phone" value="{{ $operator->phone ?? '' }}" class="col-md-2" />
    <x-form-input name="license_type" label="License Type" value="{{ $operator->license_type ?? '' }}" placeholder="SIM B2, SIO, dll" class="col-md-3" />
    <x-form-date name="license_expiry" label="License Expiry" value="{{ $operator->license_expiry ?? '' }}" class="col-md-3" />
</x-form-row>
