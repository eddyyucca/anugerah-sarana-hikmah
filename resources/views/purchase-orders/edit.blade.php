@extends('layouts.app')
@section('page-title', 'Edit PO')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li><li class="breadcrumb-item active">Edit</li>@endsection
@section('content')
<form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">@csrf @method('PUT')
<div class="erp-card mb-3"><div class="erp-card-header"><div class="section-title">PO Header</div></div><div class="erp-card-body"><div class="row g-3">
    <div class="col-md-3"><label class="form-label">PO Number</label><input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
    <div class="col-md-3"><label class="form-label">Supplier</label><select name="supplier_id" class="form-select" required style="border-radius:10px;"><option value="">--Select--</option>@foreach($suppliers as $sup)<option value="{{ $sup->id }}" {{ $purchaseOrder->supplier_id==$sup->id?'selected':'' }}>{{ $sup->supplier_name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">PO Date</label><input type="date" name="po_date" class="form-control" value="{{ $purchaseOrder->po_date->format('Y-m-d') }}" required style="border-radius:10px;"></div>
    <div class="col-md-2"><label class="form-label">Expected</label><input type="date" name="expected_date" class="form-control" value="{{ $purchaseOrder->expected_date?->format('Y-m-d') }}" style="border-radius:10px;"></div>
    <div class="col-md-12"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" value="{{ $purchaseOrder->remarks }}" style="border-radius:10px;"></div>
</div></div></div>
<div class="erp-card mb-3"><div class="erp-card-header d-flex justify-content-between align-items-center"><div class="section-title">Items</div><button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button></div>
<div class="erp-card-body"><table class="table table-modern mb-0" id="itemsTable"><thead><tr><th>Sparepart</th><th style="width:100px;">Qty</th><th style="width:150px;">Price</th><th style="width:50px;"></th></tr></thead><tbody></tbody></table></div></div>
<button type="submit" class="btn btn-danger" style="border-radius:12px;">Update PO</button> <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection
@push('scripts')
<script>
const spareparts = @json($spareparts);
let rowIdx = 0;
function addItem(spId='', qty=1, price=0) {
    const tbody = document.querySelector('#itemsTable tbody');
    const opts = spareparts.map(sp => `<option value="${sp.id}" ${sp.id==spId?'selected':''}>${sp.part_number} - ${sp.part_name}</option>`).join('');
    tbody.insertAdjacentHTML('beforeend', `<tr><td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm" required style="border-radius:10px;"><option value="">--Select--</option>${opts}</select></td><td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm" value="${qty}" min="1" required style="border-radius:10px;"></td><td><input type="number" step="0.01" name="items[${rowIdx}][unit_price]" class="form-control form-control-sm" value="${price}" min="0" required style="border-radius:10px;"></td><td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td></tr>`);
    rowIdx++;
}
document.addEventListener('DOMContentLoaded', () => {
    @foreach($purchaseOrder->items as $item)
    addItem('{{ $item->sparepart_id }}', {{ $item->qty }}, {{ $item->unit_price }});
    @endforeach
    if (!document.querySelector('#itemsTable tbody tr')) addItem();
});
</script>
@endpush
