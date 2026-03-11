@extends('layouts.app')
@section('page-title', 'Create Goods Receipt')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Goods Receipts</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-receipts.store') }}" method="POST">
    @csrf
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">GR Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">GR Number</label><input type="text" class="form-control" value="{{ $grNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Source PO <span class="text-danger">*</span></label>
                    <select name="purchase_order_id" class="form-select" required style="border-radius:10px;" id="poSelect">
                        <option value="">-- Select PO --</option>
                        @foreach($openPOs as $opo)
                        <option value="{{ $opo->id }}" {{ ($po && $po->id == $opo->id)?'selected':'' }}>{{ $opo->po_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Receipt Date <span class="text-danger">*</span></label><input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>

    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">Items</div></div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Sparepart</th><th>Warehouse Location</th><th style="width:120px;">Qty Received</th></tr></thead>
                <tbody>
                    @if($po && count($poItems) > 0)
                        @foreach($poItems as $i => $item)
                        <tr>
                            <td>{{ $item->sparepart->part_number }} - {{ $item->sparepart->part_name }}
                                <input type="hidden" name="items[{{ $i }}][sparepart_id]" value="{{ $item->sparepart_id }}">
                            </td>
                            <td>
                                <select name="items[{{ $i }}][warehouse_location_id]" class="form-select form-select-sm" style="border-radius:10px;">
                                    <option value="">-- Default --</option>
                                    @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $i }}][qty_received]" class="form-control form-control-sm" value="{{ $item->qty }}" min="1" required style="border-radius:10px;"></td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="3" class="text-center text-muted py-3">Select a PO first, or add items manually.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save GR</button>
    <a href="{{ route('goods-receipts.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection
