<<<<<<< HEAD
<x-form-row>
    <x-form-input name="supplier_code" label="Supplier Code" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}" required class="col-md-3" />
    <x-form-input name="supplier_name" label="Supplier Name" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required class="col-md-5" />
    <x-form-input name="contact_person" label="Contact Person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="col-md-4" />
    <x-form-input name="phone" label="Phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="col-md-3" />
    <x-form-input name="email" label="Email" value="{{ old('email', $supplier->email ?? '') }}" type="email" class="col-md-3" />
    <x-form-textarea name="address" label="Address" value="{{ old('address', $supplier->address ?? '') }}" rows="2" class="col-md-6" />
</x-form-row>
=======
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">Supplier Code <span class="text-danger">*</span></label><input type="text" name="supplier_code" class="form-control @error('supplier_code') is-invalid @enderror" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}" required style="border-radius:10px;">@error('supplier_code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-5"><label class="form-label">Supplier Name <span class="text-danger">*</span></label><input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required style="border-radius:10px;">@error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label class="form-label">Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" style="border-radius:10px;"></div>
    <div class="col-md-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}" style="border-radius:10px;"></div>
    <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}" style="border-radius:10px;"></div>
    <div class="col-md-6"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2" style="border-radius:10px;">{{ old('address', $supplier->address ?? '') }}</textarea></div>
</div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
