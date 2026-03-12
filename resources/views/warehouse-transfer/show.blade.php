@extends('layouts.app')
@section('page-title', $warehouseTransfer->transfer_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('warehouse-transfer.index') }}">Warehouse Transfer</a></li><li class="breadcrumb-item active">{{ $warehouseTransfer->transfer_number }}</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3 mb-3">
            <div class="d-flex justify-content-between mb-3">
                <div><div style="font-weight:800;font-size:1.1rem;">{{ $warehouseTransfer->transfer_number }}</div></div>
                @include('components.status-badge', ['status' => $warehouseTransfer->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">From</td><td><strong>{{ $warehouseTransfer->fromLocation->name ?? '-' }}</strong></td></tr>
                <tr><td class="text-muted">To</td><td><strong>{{ $warehouseTransfer->toLocation->name ?? '-' }}</strong></td></tr>
                <tr><td class="text-muted">Date</td><td>{{ $warehouseTransfer->transfer_date->format('d M Y') }}</td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $warehouseTransfer->remarks ?? '-' }}</td></tr>
                <tr><td class="text-muted">Created By</td><td>{{ $warehouseTransfer->creator->name ?? '-' }}</td></tr>
                @if($warehouseTransfer->poster)
                <tr><td class="text-muted">Sent By</td><td>{{ $warehouseTransfer->poster->name }} <div class="text-muted" style="font-size:.75rem;">{{ $warehouseTransfer->posted_at?->format('d M Y H:i') }}</div></td></tr>
                @endif
                @if($warehouseTransfer->receiver)
                <tr><td class="text-muted">Received By</td><td>{{ $warehouseTransfer->receiver->name }} <div class="text-muted" style="font-size:.75rem;">{{ $warehouseTransfer->received_at?->format('d M Y H:i') }}</div></td></tr>
                @endif
            </table>

            {{-- Workflow Status --}}
            <div class="mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle {{ in_array($warehouseTransfer->status, ['draft','sent','received']) ? 'bg-success' : 'bg-secondary' }}" style="width:12px;height:12px;"></div>
                    <span style="font-size:.85rem;">Draft Created</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle {{ in_array($warehouseTransfer->status, ['sent','received']) ? 'bg-success' : 'bg-secondary' }}" style="width:12px;height:12px;"></div>
                    <span style="font-size:.85rem;">Sent (Stock Deducted)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle {{ $warehouseTransfer->status === 'received' ? 'bg-success' : 'bg-secondary' }}" style="width:12px;height:12px;"></div>
                    <span style="font-size:.85rem;">Received (Stock Added)</span>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @if($warehouseTransfer->status === 'draft')
                <form action="{{ route('warehouse-transfer.send', $warehouseTransfer) }}" method="POST">@csrf
                    <button class="btn btn-sm btn-warning" style="border-radius:10px;" onclick="return confirm('Send transfer? Stock will be deducted from source.')"><i class="bi bi-send me-1"></i>Send</button>
                </form>
                @endif
                @if($warehouseTransfer->status === 'sent')
                <form action="{{ route('warehouse-transfer.receive', $warehouseTransfer) }}" method="POST">@csrf
                    <button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Confirm receipt? Stock will be added to destination.')"><i class="bi bi-check-lg me-1"></i>Receive</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Transfer Items</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>#</th><th>Part Number</th><th>Name</th><th>Qty</th></tr></thead>
                        <tbody>
                            @foreach($warehouseTransfer->items as $i => $item)
                            <tr><td>{{ $i+1 }}</td><td><strong>{{ $item->sparepart->part_number }}</strong></td><td>{{ $item->sparepart->part_name }}</td><td><strong>{{ $item->qty }}</strong></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
