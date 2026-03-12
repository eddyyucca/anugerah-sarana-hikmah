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
        <div class="erp-card-header">
            <div class="section-title">Items to Receive</div>
            <div class="section-subtitle">Only items with outstanding qty shown. Enter qty received (can be partial).</div>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr><th>Part Number</th><th>Part Name</th><th class="text-center">Ordered</th><th class="text-center">Already Received</th><th class="text-center" style="color:#dc2626;">Outstanding</th><th>Location</th><th style="width:130px;">Qty to Receive</th></tr></thead>
                <tbody>
                    @if($po && $poItems->count() > 0)
                        @foreach($poItems as $i => $item)
                        <tr>
                            <td>
                                <strong>{{ $item->sparepart->part_number }}</strong>
                                <input type="hidden" name="items[{{ $i }}][sparepart_id]" value="{{ $item->sparepart_id }}">
                                <input type="hidden" name="items[{{ $i }}][po_item_id]" value="{{ $item->id }}">
                            </td>
                            <td>{{ $item->sparepart->part_name }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-center text-success fw-bold">{{ $item->qty_received }}</td>
                            <td class="text-center text-danger fw-bold">{{ $item->qty_remaining }}</td>
                            <td>
                                <select name="items[{{ $i }}][warehouse_location_id]" class="form-select form-select-sm" style="border-radius:10px;">
                                    <option value="">-- Default --</option>
                                    @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][qty_received]" class="form-control form-control-sm" value="{{ $item->qty_remaining }}" min="0" max="{{ $item->qty_remaining }}" required style="border-radius:10px;">
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center text-muted py-3">Select a PO to see outstanding items.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save GR</button>
    <a href="{{ route('goods-receipts.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection
