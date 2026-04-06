@extends('layouts.app')
@section('page-title', 'Create Goods Issue')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-issues.index') }}">Goods Issues</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-issues.store') }}" method="POST" id="giForm">
    @csrf
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
                        <option value="">-- None --</option>
                        @foreach($workOrders as $wo)
                        <option value="{{ $wo->id }}" {{ (isset($woId) && $woId == $wo->id)?'selected':'' }}>{{ $wo->wo_number }}</option>
                        @endforeach
                    </select>
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
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th>Location</th><th style="width:120px;">Qty</th><th style="width:50px;"></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </x-card>

    <x-button type="submit" variant="danger">Save GI</x-button>
    <a href="{{ route('goods-issues.index') }}" class="btn btn-light">Cancel</a>
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
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm tom-select" required><option value="">-- Select --</option>${spOpts}</select></td>
        <td><select name="items[${rowIdx}][warehouse_location_id]" class="form-select form-select-sm tom-select"><option value="">-- Default --</option>${locOpts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty_issued]" class="form-control form-control-sm" value="1" min="1" required></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td>
    </tr>`);
    rowIdx++;
}
document.addEventListener('DOMContentLoaded', () => { addItem(); });
</script>
@endpush
