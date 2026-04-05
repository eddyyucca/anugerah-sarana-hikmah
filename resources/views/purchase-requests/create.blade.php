@extends('layouts.app')
@section('page-title', 'Create Purchase Request')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Purchase Requests</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('purchase-requests.store') }}" method="POST" id="prForm">
    @csrf
<<<<<<< HEAD
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">PR Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="PR Number">
                    <input type="text" class="form-control" value="{{ $prNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Request Date" required>
                    <input type="date" name="request_date" class="form-control" value="{{ old('request_date', date('Y-m-d')) }}" required>
                </x-form-group>
            </div>
            <div class="col-md-6">
                <x-form-group label="Remarks">
                    <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
                </x-form-group>
            </div>
        </div>
    </x-card>

    <x-card class="mb-3">
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="section-title">Items</div>
                <x-button type="button" variant="outline-danger" size="sm" onclick="addItem()">
                    <i class="bi bi-plus-lg me-1"></i>Add Item
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

    <x-button type="submit" variant="danger">Save PR</x-button>
    <a href="{{ route('purchase-requests.index') }}" class="btn btn-light">Cancel</a>
=======
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">PR Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">PR Number</label><input type="text" class="form-control" value="{{ $prNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3"><label class="form-label">Request Date <span class="text-danger">*</span></label><input type="date" name="request_date" class="form-control" value="{{ old('request_date', date('Y-m-d')) }}" required style="border-radius:10px;"></div>
                <div class="col-md-6"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>

    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Items</div>
            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>
        <div class="erp-card-body">
            <div class="table-responsive">
                <table class="table table-modern mb-0" id="itemsTable">
                    <thead><tr><th>Sparepart</th><th style="width:120px;">Qty</th><th>Notes</th><th style="width:60px;"></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save PR</button>
    <a href="{{ route('purchase-requests.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
let rowIdx = 0;

function addItem(sparepartId = '', qty = 1, notes = '') {
    const tbody = document.querySelector('#itemsTable tbody');
    const opts = spareparts.map(sp => `<option value="${sp.id}" ${sp.id == sparepartId ? 'selected' : ''}>${sp.part_number} - ${sp.part_name} (${sp.uom})</option>`).join('');
    tbody.insertAdjacentHTML('beforeend', `<tr>
<<<<<<< HEAD
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm tom-select" required><option value="">-- Select --</option>${opts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm" value="${qty}" min="1" required></td>
        <td><input type="text" name="items[${rowIdx}][notes]" class="form-control form-control-sm" value="${notes}"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td>
=======
        <td><select name="items[${rowIdx}][sparepart_id]" class="form-select form-select-sm" required style="border-radius:10px;"><option value="">-- Select --</option>${opts}</select></td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm" value="${qty}" min="1" required style="border-radius:10px;"></td>
        <td><input type="text" name="items[${rowIdx}][notes]" class="form-control form-control-sm" value="${notes}" style="border-radius:10px;"></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" style="border-radius:8px;"><i class="bi bi-x-lg"></i></button></td>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    </tr>`);
    rowIdx++;
}

document.addEventListener('DOMContentLoaded', () => { addItem(); });
</script>
@endpush
