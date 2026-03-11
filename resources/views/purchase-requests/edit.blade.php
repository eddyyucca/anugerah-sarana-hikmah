@extends('layouts.app')
@section('page-title', 'Edit PR')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Purchase Requests</a></li><li class="breadcrumb-item active">Edit</li>@endsection

@section('content')
<form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST">
    @csrf @method('PUT')
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">PR Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">PR Number</label><input type="text" class="form-control" value="{{ $purchaseRequest->pr_number }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3"><label class="form-label">Request Date</label><input type="date" name="request_date" class="form-control" value="{{ $purchaseRequest->request_date->format('Y-m-d') }}" required style="border-radius:10px;"></div>
                <div class="col-md-6"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" value="{{ $purchaseRequest->remarks }}" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>
    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Items</div>
            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable"><thead><tr><th>Sparepart</th><th style="width:120px;">Qty</th><th>Notes</th><th style="width:60px;"></th></tr></thead><tbody></tbody></table>
        </div>
    </div>
    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Update PR</button>
    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
let rowIdx = 0;
function addItem(sparepartId = '', qty = 1, notes = '') {
    const tbody = document.querySelector('#itemsTable tbody');
    const opts = spareparts.map(sp => `<option value="${sp.id}" ${sp.id == sparepartId ? 'selected' : ''}>${sp.part_number} - ${sp.part_name}</option>`).join('');
    tbody.insertAdjacentHTML('beforeend', `<tr><td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm" required style="border-radius:10px;"><option value="">-- Select --</option>${opts}</select></td><td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm" value="${qty}" min="1" required style="border-radius:10px;"></td><td><input type="text" name="items[${rowIdx}][notes]" class="form-control form-control-sm" value="${notes}" style="border-radius:10px;"></td><td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td></tr>`);
    rowIdx++;
}
document.addEventListener('DOMContentLoaded', () => {
    @foreach($purchaseRequest->items as $item)
    addItem('{{ $item->sparepart_id }}', {{ $item->qty }}, '{{ $item->notes ?? '' }}');
    @endforeach
    if (!document.querySelector('#itemsTable tbody tr')) addItem();
});
</script>
@endpush
