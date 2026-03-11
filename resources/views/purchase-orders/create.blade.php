@extends('layouts.app')
@section('page-title', 'Create Purchase Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('purchase-orders.store') }}" method="POST">
    @csrf
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">PO Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-2"><label class="form-label">PO Number</label><input type="text" class="form-control" value="{{ $poNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Source PR</label>
                    <select name="purchase_request_id" class="form-select" style="border-radius:10px;">
                        <option value="">-- None --</option>
                        @foreach($approvedPRs as $apr)<option value="{{ $apr->id }}" {{ ($pr && $pr->id == $apr->id)?'selected':'' }}>{{ $apr->pr_number }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select" required style="border-radius:10px;">
                        <option value="">-- Select --</option>
                        @foreach($suppliers as $sup)<option value="{{ $sup->id }}">{{ $sup->supplier_code }} - {{ $sup->supplier_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label">PO Date <span class="text-danger">*</span></label><input type="date" name="po_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:10px;"></div>
                <div class="col-md-2"><label class="form-label">Expected Date</label><input type="date" name="expected_date" class="form-control" style="border-radius:10px;"></div>
                <div class="col-md-12"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>
    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Items</div>
            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable"><thead><tr><th>Sparepart</th><th style="width:100px;">Qty</th><th style="width:150px;">Unit Price</th><th style="width:150px;">Total</th><th style="width:50px;"></th></tr></thead><tbody></tbody></table>
        </div>
    </div>
    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save PO</button>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
let rowIdx = 0;
function addItem(spId = '', qty = 1, price = 0) {
    const tbody = document.querySelector('#itemsTable tbody');
    const opts = spareparts.map(sp => `<option value="${sp.id}" data-price="${sp.unit_price}" ${sp.id == spId ? 'selected' : ''}>${sp.part_number} - ${sp.part_name}</option>`).join('');
    const idx = rowIdx;
    tbody.insertAdjacentHTML('beforeend', `<tr>
        <td><select name="items[${idx}][sparepart_id]" class="form-select form-select-sm sp-select" data-row="${idx}" required style="border-radius:10px;"><option value="">-- Select --</option>${opts}</select></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control form-control-sm qty-input" data-row="${idx}" value="${qty}" min="1" required style="border-radius:10px;"></td>
        <td><input type="number" step="0.01" name="items[${idx}][unit_price]" class="form-control form-control-sm price-input" data-row="${idx}" value="${price}" min="0" required style="border-radius:10px;"></td>
        <td><input type="text" class="form-control form-control-sm total-display" data-row="${idx}" readonly style="border-radius:10px;background:#f8f9fa;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td>
    </tr>`);
    rowIdx++;
    calcRow(idx);
}
function calcRow(idx) {
    const q = parseFloat(document.querySelector(`.qty-input[data-row="${idx}"]`)?.value || 0);
    const p = parseFloat(document.querySelector(`.price-input[data-row="${idx}"]`)?.value || 0);
    const t = document.querySelector(`.total-display[data-row="${idx}"]`);
    if (t) t.value = (q * p).toLocaleString('id-ID');
}
document.addEventListener('input', e => {
    if (e.target.matches('.qty-input,.price-input')) calcRow(e.target.dataset.row);
});
document.addEventListener('change', e => {
    if (e.target.matches('.sp-select')) {
        const opt = e.target.selectedOptions[0];
        const price = opt?.dataset?.price || 0;
        const idx = e.target.dataset.row;
        document.querySelector(`.price-input[data-row="${idx}"]`).value = price;
        calcRow(idx);
    }
});
document.addEventListener('DOMContentLoaded', () => {
    @if($pr && count($prItems) > 0)
        @foreach($prItems as $item)
        addItem('{{ $item->sparepart_id }}', {{ $item->qty }}, {{ $item->sparepart->unit_price ?? 0 }});
        @endforeach
    @else
        addItem();
    @endif
});
</script>
@endpush
