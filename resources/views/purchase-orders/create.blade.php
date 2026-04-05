@extends('layouts.app')
@section('page-title', 'Create Purchase Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('purchase-orders.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">PO Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-2">
                <x-form-group label="PO Number">
                    <input type="text" class="form-control" value="{{ $poNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Source PR">
                    <select name="purchase_request_id" class="form-select tom-select">
                        <option value="">-- None --</option>
                        @foreach($approvedPRs as $apr)<option value="{{ $apr->id }}" {{ ($pr && $pr->id == $apr->id)?'selected':'' }}>{{ $apr->pr_number }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Supplier" required>
                    <select name="supplier_id" class="form-select tom-select" required>
                        <option value="">-- Select --</option>
                        @foreach($suppliers as $sup)<option value="{{ $sup->id }}">{{ $sup->supplier_code }} - {{ $sup->supplier_name }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="PO Date" required>
                    <input type="date" name="po_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Expected Date">
                    <input type="date" name="expected_date" class="form-control">
                </x-form-group>
            </div>
            <div class="col-md-12">
                <x-form-group label="Remarks">
                    <input type="text" name="remarks" class="form-control">
                </x-form-group>
            </div>
        </div>
    </x-card>
    <x-card class="mb-3">
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="section-title">Items</div>
                <x-button type="button" variant="outline-danger" size="sm" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</x-button>
            </div>
        </x-slot:header>
        <div class="table-responsive">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th style="width:100px;">Qty</th><th style="width:150px;">Unit Price</th><th style="width:150px;">Total</th><th style="width:50px;"></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </x-card>
    <x-button type="submit" variant="danger">Save PO</x-button>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">Cancel</a>
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
        <td><select name="items[${idx}][sparepart_id]" class="form-select form-select-sm sp-select tom-select" data-row="${idx}" required><option value="">-- Select --</option>${opts}</select></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control form-control-sm qty-input" data-row="${idx}" value="${qty}" min="1" required></td>
        <td><input type="number" step="0.01" name="items[${idx}][unit_price]" class="form-control form-control-sm price-input" data-row="${idx}" value="${price}" min="0" required></td>
        <td><input type="text" class="form-control form-control-sm total-display" data-row="${idx}" readonly style="background:#f8f9fa;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td>
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
