@extends('layouts.app')
@section('page-title', 'New Warehouse Transfer')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('warehouse-transfer.index') }}">Warehouse Transfer</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<form action="{{ route('warehouse-transfer.store') }}" method="POST">@csrf
<div class="erp-card mb-3">
    <div class="erp-card-header"><div class="section-title">Transfer Header</div></div>
    <div class="erp-card-body">
        <div class="row g-3">
            <x-form-select name="from_location_id" label="From Location" required class="col-md-3">
                <option value="">--Select--</option>
                @foreach($locations as $l)
                    <option value="{{ $l->id }}">{{ $l->code }} - {{ $l->name }}</option>
                @endforeach
            </x-form-select>
            <x-form-select name="to_location_id" label="To Location" required class="col-md-3">
                <option value="">--Select--</option>
                @foreach($locations as $l)
                    <option value="{{ $l->id }}">{{ $l->code }} - {{ $l->name }}</option>
                @endforeach
            </x-form-select>
            <x-form-date name="transfer_date" label="Date" :value="date('Y-m-d')" required class="col-md-3" />
            <x-form-input name="remarks" label="Remarks" type="text" class="col-md-3" />
        </div>
    </div>
</div>
<div class="erp-card mb-3">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title">Items</div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="addItem()"><i class="bi bi-plus-lg me-1"></i>Add</button>
    </div>
    <div class="erp-card-body">
        <table class="table table-modern mb-0" id="itemsTable">
            <thead>
                <tr>
                    <th>Sparepart</th>
                    <th style="width:120px;">Qty</th>
                    <th style="width:50px;"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<button type="submit" class="btn btn-danger">Save Transfer</button>
<a href="{{ route('warehouse-transfer.index') }}" class="btn btn-light">Cancel</a>
</form>
@endsection
@push('scripts')
<script>
const sp = @json($spareparts);
let ri=0;
function addItem(){
    const opts=sp.map(s=>`<option value="${s.id}">${s.part_number} - ${s.part_name} (Stk: ${s.stock_on_hand})</option>`).join('');
    document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend',`<tr><td><select name="items[${ri}][sparepart_id]" class="form-select form-select-sm tom-select" required><option value="">--Select--</option>${opts}</select></td><td><input type="number" name="items[${ri}][qty]" class="form-control form-control-sm" min="1" value="1" required></td><td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x-lg"></i></button></td></tr>`);
    ri++;
}
document.addEventListener('DOMContentLoaded',()=>addItem());
</script>
@endpush
