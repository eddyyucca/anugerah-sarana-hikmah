@extends('layouts.app')
@section('page-title', 'New Stock Opname')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}">Stock Opname</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<form action="{{ route('stock-opname.store') }}" method="POST">@csrf
<div class="erp-card mb-3">
    <div class="erp-card-header"><div class="section-title">Opname Header</div></div>
    <div class="erp-card-body"><div class="row g-3">
        <div class="col-md-3"><label class="form-label">Date</label><input type="date" name="opname_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:10px;"></div>
        <div class="col-md-3"><label class="form-label">Location</label><select name="warehouse_location_id" class="form-select" style="border-radius:10px;"><option value="">All Location</option>@foreach($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" style="border-radius:10px;"></div>
    </div></div>
</div>
<div class="erp-card mb-3">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title">Items Count</div>
        <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button>
    </div>
    <div class="erp-card-body"><table class="table table-modern mb-0" id="itemsTable"><thead><tr><th>Sparepart</th><th>System Qty</th><th style="width:140px;">Physical Qty</th><th>Notes</th><th style="width:50px;"></th></tr></thead><tbody></tbody></table></div>
</div>
<button type="submit" class="btn btn-danger" style="border-radius:12px;">Save Opname</button>
<a href="{{ route('stock-opname.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection
@push('scripts')
<script>
const spareparts = @json($spareparts);
let ri = 0;
function addItem() {
    const opts = spareparts.map(s => `<option value="${s.id}" data-stk="${s.stock_on_hand}">${s.part_number} - ${s.part_name} (Stk: ${s.stock_on_hand})</option>`).join('');
    document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', `<tr>
        <td><select name="items[${ri}][sparepart_id]" class="form-select form-select-sm sp-sel" data-r="${ri}" required style="border-radius:10px;"><option value="">--Select--</option>${opts}</select></td>
        <td><span class="sys-qty" data-r="${ri}">-</span></td>
        <td><input type="number" name="items[${ri}][physical_qty]" class="form-control form-control-sm" min="0" required style="border-radius:10px;"></td>
        <td><input type="text" name="items[${ri}][notes]" class="form-control form-control-sm" style="border-radius:10px;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td>
    </tr>`); ri++;
}
document.addEventListener('change', e => {
    if (e.target.matches('.sp-sel')) { const o=e.target.selectedOptions[0]; document.querySelector(`.sys-qty[data-r="${e.target.dataset.r}"]`).textContent=o?.dataset?.stk||'-'; }
});
document.addEventListener('DOMContentLoaded', () => addItem());
</script>
@endpush
