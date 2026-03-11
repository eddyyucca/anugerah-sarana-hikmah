@extends('layouts.app')
@section('page-title', $goodsReceipt->gr_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Goods Receipts</a></li><li class="breadcrumb-item active">{{ $goodsReceipt->gr_number }}</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><div style="font-weight:800;font-size:1.2rem;">{{ $goodsReceipt->gr_number }}</div><div class="text-muted" style="font-size:.85rem;">{{ $goodsReceipt->receipt_date->format('d M Y') }}</div></div>
                @include('components.status-badge', ['status' => $goodsReceipt->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">PO Ref</td><td>{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}</td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $goodsReceipt->remarks ?? '-' }}</td></tr>
                @if($goodsReceipt->postedByUser)<tr><td class="text-muted">Posted By</td><td>{{ $goodsReceipt->postedByUser->name }}</td></tr>@endif
                @if($goodsReceipt->posted_at)<tr><td class="text-muted">Posted At</td><td>{{ $goodsReceipt->posted_at->format('d M Y H:i') }}</td></tr>@endif
            </table>
            @if($goodsReceipt->status === 'draft')
            <form action="{{ route('goods-receipts.post', $goodsReceipt) }}" method="POST">@csrf
                <button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Post this GR? Stock will be updated.')"><i class="bi bi-check-lg me-1"></i>Post GR</button>
            </form>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Items Received</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>#</th><th>Part Number</th><th>Part Name</th><th>Location</th><th>Qty Received</th></tr></thead>
                        <tbody>
                            @foreach($goodsReceipt->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->sparepart->part_number }}</td>
                                <td>{{ $item->sparepart->part_name }}</td>
                                <td>{{ $item->warehouseLocation->name ?? '-' }}</td>
                                <td>{{ $item->qty_received }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
