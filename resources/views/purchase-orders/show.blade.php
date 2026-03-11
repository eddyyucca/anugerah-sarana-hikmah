@extends('layouts.app')
@section('page-title', $purchaseOrder->po_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li><li class="breadcrumb-item active">{{ $purchaseOrder->po_number }}</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><div style="font-weight:800;font-size:1.2rem;">{{ $purchaseOrder->po_number }}</div><div class="text-muted" style="font-size:.85rem;">{{ $purchaseOrder->po_date->format('d M Y') }}</div></div>
                @include('components.status-badge', ['status' => $purchaseOrder->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Supplier</td><td>{{ $purchaseOrder->supplier->supplier_name ?? '-' }}</td></tr>
                <tr><td class="text-muted">PR Ref</td><td>{{ $purchaseOrder->purchaseRequest->pr_number ?? '-' }}</td></tr>
                <tr><td class="text-muted">Expected</td><td>{{ $purchaseOrder->expected_date?->format('d M Y') ?? '-' }}</td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $purchaseOrder->remarks ?? '-' }}</td></tr>
                <tr><td class="text-muted">Total</td><td><strong>IDR {{ number_format($purchaseOrder->items->sum('total_price'), 0, ',', '.') }}</strong></td></tr>
            </table>
            <div class="d-flex flex-wrap gap-2">
                @if($purchaseOrder->status === 'draft')
                    <form action="{{ route('purchase-orders.issue', $purchaseOrder) }}" method="POST">@csrf<button class="btn btn-sm btn-warning" style="border-radius:10px;">Issue PO</button></form>
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">Edit</a>
                @endif
                @if(in_array($purchaseOrder->status, ['issued', 'partial']))
                    <a href="{{ route('goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="btn btn-sm btn-success" style="border-radius:10px;"><i class="bi bi-box-arrow-in-down me-1"></i>Create GR</a>
                    <form action="{{ route('purchase-orders.close', $purchaseOrder) }}" method="POST">@csrf<button class="btn btn-sm btn-secondary" style="border-radius:10px;" onclick="return confirm('Close this PO?')">Close PO</button></form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Items</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>#</th><th>Part Number</th><th>Part Name</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->sparepart->part_number }}</td>
                                <td>{{ $item->sparepart->part_name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><td colspan="5" class="text-end"><strong>Total</strong></td><td><strong>{{ number_format($purchaseOrder->items->sum('total_price'), 0, ',', '.') }}</strong></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
