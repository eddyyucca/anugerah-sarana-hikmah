@extends('layouts.app')
@section('page-title', 'Create Consumable PR')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('consumable-pr.index') }}">Consumable PR</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('consumable-pr.store') }}" method="POST">
    @csrf
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title"><i class="bi bi-droplet me-2"></i>Consumable PR Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">PR Number</label>
                    <input type="text" class="form-control" value="{{ $prNumber }}" readonly style="background:#f8f9fa;">
                </div>
                <x-form-date name="request_date" label="Request Date" :value="date('Y-m-d')" required class="col-md-3" />
                <x-form-input name="remarks" label="Remarks" type="text" placeholder="Consumable restock..." class="col-md-6" />
            </div>
        </div>
    </div>

    @if($spareparts->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>No consumable spareparts found. Mark spareparts as consumable in Sparepart Master first.
    </div>
    @else
    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Consumable Items</div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead>
                    <tr>
                        <th>Consumable Part</th>
                        <th>Stock</th>
                        <th>Min</th>
                        <th style="width:120px;">Order Qty</th>
                        <th>Notes</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    @endif

    <button type="submit" class="btn btn-danger">Save Consumable PR</button>
    <a href="{{ route('consumable-pr.index') }}" class="btn btn-light">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
const parts = @json($spareparts);
let ri = 0;
function addItem() {
    const opts = parts.map(p => `<option value="${p.id}" data-stk="${p.stock_on_hand}" data-min="${p.minimum_stock}">${p.part_number} - ${p.part_name} (${p.uom})</option>`).join('');
    document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', `<tr>
        <td><select name="items[${ri}][sparepart_id]" class="form-select form-select-sm tom-select sp-sel" data-r="${ri}" required><option value="">--Select Consumable--</option>${opts}</select></td>
        <td class="stk-val" data-r="${ri}">-</td>
        <td class="min-val" data-r="${ri}">-</td>
        <td><input type="number" name="items[${ri}][qty]" class="form-control form-control-sm" min="1" value="1" required></td>
        <td><input type="text" name="items[${ri}][notes]" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td>
    </tr>`);
    ri++;
}
document.addEventListener('change', e => {
    if (e.target.matches('.sp-sel')) {
        const o = e.target.selectedOptions[0], r = e.target.dataset.r;
        document.querySelector(`.stk-val[data-r="${r}"]`).textContent = o?.dataset?.stk || '-';
        document.querySelector(`.min-val[data-r="${r}"]`).textContent = o?.dataset?.min || '-';
    }
});
document.addEventListener('DOMContentLoaded', () => addItem());
</script>
@endpush
