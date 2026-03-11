@extends('layouts.app')
@section('page-title', 'Create Goods Issue')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-issues.index') }}">Goods Issues</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-issues.store') }}" method="POST" id="giForm">
    @csrf
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">GI Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">GI Number</label><input type="text" class="form-control" value="{{ $giNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Work Order (optional)</label>
                    <select name="work_order_id" class="form-select" style="border-radius:10px;">
                        <option value="">-- None --</option>
                        @foreach($workOrders as $wo)
                        <option value="{{ $wo->id }}" {{ (isset($woId) && $woId == $wo->id)?'selected':'' }}>{{ $wo->wo_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Issue Date <span class="text-danger">*</span></label><input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>

    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Items</div>
            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th>Location</th><th style="width:120px;">Qty</th><th style="width:50px;"></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save GI</button>
    <a href="{{ route('goods-issues.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
const locations = @json($locations);
let rowIdx = 0;

function addItem() {
    const tbody = document.querySelector('#itemsTable tbody');
    const spOpts = spareparts.map(sp => `<option value="${sp.id}">${sp.part_number} - ${sp.part_name} (Stock: ${sp.stock_on_hand})</option>`).join('');
    const locOpts = locations.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
    tbody.insertAdjacentHTML('beforeend', `<tr>
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm" required style="border-radius:10px;"><option value="">-- Select --</option>${spOpts}</select></td>
        <td><select name="items[${rowIdx}][warehouse_location_id]" class="form-select form-select-sm" style="border-radius:10px;"><option value="">-- Default --</option>${locOpts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty_issued]" class="form-control form-control-sm" value="1" min="1" required style="border-radius:10px;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td>
    </tr>`);
    rowIdx++;
}
document.addEventListener('DOMContentLoaded', () => { addItem(); });
</script>
@endpush
