<x-form-row>
    <x-form-input name="supplier_code" label="Supplier Code" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}" required class="col-md-3" />
    <x-form-input name="supplier_name" label="Supplier Name" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required class="col-md-5" />
    <x-form-input name="contact_person" label="Contact Person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="col-md-4" />
    <x-form-input name="phone" label="Phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="col-md-3" />
    <x-form-input name="email" label="Email" value="{{ old('email', $supplier->email ?? '') }}" type="email" class="col-md-3" />
    <x-form-textarea name="address" label="Address" value="{{ old('address', $supplier->address ?? '') }}" rows="2" class="col-md-6" />
</x-form-row>
