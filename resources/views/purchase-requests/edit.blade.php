@extends('layouts.app')
@section('page-title', 'Edit PR')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Purchase Requests</a></li><li class="breadcrumb-item active">Edit</li>@endsection

@section('content')
<form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST">
    @csrf @method('PUT')
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">PR Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="PR Number">
                    <input type="text" class="form-control" value="{{ $purchaseRequest->pr_number }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Request Date">
                    <input type="date" name="request_date" class="form-control" value="{{ $purchaseRequest->request_date->format('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-6">
                <x-form-group label="Remarks">
                    <input type="text" name="remarks" class="form-control" value="{{ $purchaseRequest->remarks }}">
                </x-form-group>
            </div>
        </div>
    </x-card>
    <x-card class="mb-3">
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="section-title">Items</div>
                <x-button type="button" variant="outline-danger" size="sm" onclick="addItem()">
                    <i class="bi bi-plus-lg me-1"></i>Add
                </x-button>
            </div>
        </x-slot:header>
        <div class="table-responsive">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th style="width:120px;">Qty</th><th>Notes</th><th style="width:60px;"></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </x-card>
    <x-button type="submit" variant="danger">Update PR</x-button>
    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-light">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
let rowIdx = 0;
function addItem(sparepartId = '', qty = 1, notes = '') {
    const tbody = document.querySelector('#itemsTable tbody');
    const opts = spareparts.map(sp => `<option value="${sp.id}" ${sp.id == sparepartId ? 'selected' : ''}>${sp.part_number} - ${sp.part_name}</option>`).join('');
    tbody.insertAdjacentHTML('beforeend', `<tr><td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm tom-select" required><option value="">-- Select --</option>${opts}</select></td><td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm" value="${qty}" min="1" required></td><td><input type="text" name="items[${rowIdx}][notes]" class="form-control form-control-sm" value="${notes}"></td><td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td></tr>`);
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
