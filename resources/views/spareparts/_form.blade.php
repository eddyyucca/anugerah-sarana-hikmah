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
