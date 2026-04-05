@extends('layouts.app')
@section('page-title', 'Create Goods Issue')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-issues.index') }}">Goods Issues</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-issues.store') }}" method="POST" id="giForm">
    @csrf
<<<<<<< HEAD
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">GI Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="GI Number">
                    <input type="text" class="form-control" value="{{ $giNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Work Order (optional)">
                    <select name="work_order_id" class="form-select tom-select">
=======
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">GI Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">GI Number</label><input type="text" class="form-control" value="{{ $giNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Work Order (optional)</label>
                    <select name="work_order_id" class="form-select" style="border-radius:10px;">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
                        <option value="">-- None --</option>
                        @foreach($workOrders as $wo)
                        <option value="{{ $wo->id }}" {{ (isset($woId) && $woId == $wo->id)?'selected':'' }}>{{ $wo->wo_number }}</option>
                        @endforeach
                    </select>
<<<<<<< HEAD
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Issue Date" required>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-3">
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
                <x-button type="button" variant="outline-danger" size="sm" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add Item</x-button>
            </div>
        </x-slot:header>
        <div class="table-responsive">
=======
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
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th>Location</th><th style="width:120px;">Qty</th><th style="width:50px;"></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
<<<<<<< HEAD
    </x-card>

    <x-button type="submit" variant="danger">Save GI</x-button>
    <a href="{{ route('goods-issues.index') }}" class="btn btn-light">Cancel</a>
=======
    </div>

    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save GI</button>
    <a href="{{ route('goods-issues.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
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
<<<<<<< HEAD
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm tom-select" required><option value="">-- Select --</option>${spOpts}</select></td>
        <td><select name="items[${rowIdx}][warehouse_location_id]" class="form-select form-select-sm tom-select"><option value="">-- Default --</option>${locOpts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty_issued]" class="form-control form-control-sm" value="1" min="1" required></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td>
=======
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm" required style="border-radius:10px;"><option value="">-- Select --</option>${spOpts}</select></td>
        <td><select name="items[${rowIdx}][warehouse_location_id]" class="form-select form-select-sm" style="border-radius:10px;"><option value="">-- Default --</option>${locOpts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty_issued]" class="form-control form-control-sm" value="1" min="1" required style="border-radius:10px;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    </tr>`);
    rowIdx++;
}
document.addEventListener('DOMContentLoaded', () => { addItem(); });
</script>
@endpush
