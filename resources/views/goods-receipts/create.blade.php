@extends('layouts.app')
@section('page-title', 'Create Goods Receipt')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Goods Receipts</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-receipts.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">GR Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="GR Number">
                    <input type="text" class="form-control" value="{{ $grNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Source PO" required>
                    <select name="purchase_order_id" class="form-select tom-select" required id="poSelect">
                        <option value="">-- Select PO --</option>
                        @foreach($openPOs as $opo)
                        <option value="{{ $opo->id }}" {{ ($po && $po->id == $opo->id)?'selected':'' }}>{{ $opo->po_number }}</option>
                        @endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Receipt Date" required>
                    <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
            <div>
                <div class="section-title">Items to Receive</div>
                <div class="section-subtitle">Only items with outstanding qty shown. Enter qty received (can be partial).</div>
            </div>
        </x-slot:header>
        <div class="table-responsive">
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
                                <select name="items[{{ $i }}][warehouse_location_id]" class="form-select form-select-sm tom-select">
                                    <option value="">-- Default --</option>
                                    @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][qty_received]" class="form-control form-control-sm" value="{{ $item->qty_remaining }}" min="0" max="{{ $item->qty_remaining }}" required>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center text-muted py-3">Select a PO to see outstanding items.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>

    <x-button type="submit" variant="danger">Save GR</x-button>
    <a href="{{ route('goods-receipts.index') }}" class="btn btn-light">Cancel</a>
</form>
@endsection
