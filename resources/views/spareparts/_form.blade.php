<<<<<<< HEAD
<x-form-row>
    <x-form-input name="part_number" label="Part Number" value="{{ old('part_number', $sparepart->part_number ?? '') }}" required class="col-md-3" />
    <x-form-input name="part_name" label="Part Name" value="{{ old('part_name', $sparepart->part_name ?? '') }}" required class="col-md-5" />
    <x-form-select name="category_id" label="Category" class="col-md-4">
        <option value="">-- Select --</option>
        @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $sparepart->category_id ?? '')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach
    </x-form-select>
    <x-form-input name="unit_price" label="Unit Price" value="{{ old('unit_price', $sparepart->unit_price ?? 0) }}" type="number" step="0.01" class="col-md-3" />
    <x-form-input name="uom" label="UOM" value="{{ old('uom', $sparepart->uom ?? 'PCS') }}" class="col-md-2" />
    <x-form-input name="minimum_stock" label="Min Stock" value="{{ old('minimum_stock', $sparepart->minimum_stock ?? 0) }}" type="number" class="col-md-2" />
    @if(!isset($sparepart))
    <x-form-input name="stock_on_hand" label="Initial Stock" value="{{ old('stock_on_hand', 0) }}" type="number" class="col-md-2" />
    @endif
</x-form-row>
=======
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Part Number <span class="text-danger">*</span></label>
        <input type="text" name="part_number" class="form-control @error('part_number') is-invalid @enderror" value="{{ old('part_number', $sparepart->part_number ?? '') }}" required style="border-radius:10px;">
        @error('part_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
        <label class="form-label">Part Name <span class="text-danger">*</span></label>
        <input type="text" name="part_name" class="form-control @error('part_name') is-invalid @enderror" value="{{ old('part_name', $sparepart->part_name ?? '') }}" required style="border-radius:10px;">
        @error('part_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" style="border-radius:10px;">
            <option value="">-- Select --</option>
            @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $sparepart->category_id ?? '')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Unit Price</label>
        <input type="number" step="0.01" name="unit_price" class="form-control" value="{{ old('unit_price', $sparepart->unit_price ?? 0) }}" style="border-radius:10px;">
    </div>
    <div class="col-md-2">
        <label class="form-label">UOM</label>
        <input type="text" name="uom" class="form-control" value="{{ old('uom', $sparepart->uom ?? 'PCS') }}" style="border-radius:10px;">
    </div>
    <div class="col-md-2">
        <label class="form-label">Min Stock</label>
        <input type="number" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', $sparepart->minimum_stock ?? 0) }}" style="border-radius:10px;">
    </div>
    @if(!isset($sparepart))
    <div class="col-md-2">
        <label class="form-label">Initial Stock</label>
        <input type="number" name="stock_on_hand" class="form-control" value="{{ old('stock_on_hand', 0) }}" style="border-radius:10px;">
    </div>
    @endif
</div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
