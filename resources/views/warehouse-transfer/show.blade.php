@extends('layouts.app')
@section('page-title', $warehouseTransfer->transfer_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('warehouse-transfer.index') }}">Warehouse Transfer</a></li><li class="breadcrumb-item active">{{ $warehouseTransfer->transfer_number }}</li>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between mb-3">
                <div><div style="font-weight:800;font-size:1.1rem;">{{ $warehouseTransfer->transfer_number }}</div></div>
                @include('components.status-badge', ['status' => $warehouseTransfer->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">From</td><td>{{ $warehouseTransfer->fromLocation->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">To</td><td>{{ $warehouseTransfer->toLocation->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Date</td><td>{{ $warehouseTransfer->transfer_date->format('d M Y') }}</td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $warehouseTransfer->remarks ?? '-' }}</td></tr>
            </table>
            @if($warehouseTransfer->status === 'draft')
            <form action="{{ route('warehouse-transfer.post', $warehouseTransfer) }}" method="POST">@csrf
                <button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Post transfer? Stock will be moved.')"><i class="bi bi-check-lg me-1"></i>Post Transfer</button>
            </form>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Transfer Items</div></div>
            <div class="erp-card-body"><div class="table-responsive"><table class="table table-modern mb-0">
                <thead><tr><th>#</th><th>Part Number</th><th>Name</th><th>Qty</th></tr></thead>
                <tbody>
                    @foreach($warehouseTransfer->items as $i => $item)
                    <tr><td>{{ $i+1 }}</td><td>{{ $item->sparepart->part_number }}</td><td>{{ $item->sparepart->part_name }}</td><td><strong>{{ $item->qty }}</strong></td></tr>
                    @endforeach
                </tbody>
            </table></div></div>
        </div>
    </div>
</div>
@endsection
