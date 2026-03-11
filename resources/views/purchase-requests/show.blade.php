@extends('layouts.app')
@section('page-title', $purchaseRequest->pr_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Purchase Requests</a></li><li class="breadcrumb-item active">{{ $purchaseRequest->pr_number }}</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div style="font-weight:800;font-size:1.2rem;">{{ $purchaseRequest->pr_number }}</div>
                    <div class="text-muted" style="font-size:.85rem;">{{ $purchaseRequest->request_date->format('d M Y') }}</div>
                </div>
                @include('components.status-badge', ['status' => $purchaseRequest->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Requester</td><td>{{ $purchaseRequest->requester->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $purchaseRequest->remarks ?? '-' }}</td></tr>
                @if($purchaseRequest->approver)<tr><td class="text-muted">Approved By</td><td>{{ $purchaseRequest->approver->name }}</td></tr>@endif
                @if($purchaseRequest->approved_at)<tr><td class="text-muted">Approved At</td><td>{{ $purchaseRequest->approved_at->format('d M Y H:i') }}</td></tr>@endif
            </table>

            <div class="d-flex flex-wrap gap-2">
                @if($purchaseRequest->status === 'draft')
                    <form action="{{ route('purchase-requests.submit', $purchaseRequest) }}" method="POST">@csrf
                        <button class="btn btn-sm btn-warning" style="border-radius:10px;">Submit for Approval</button>
                    </form>
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">Edit</a>
                @endif
                @if($purchaseRequest->status === 'submitted')
                    <form action="{{ route('purchase-requests.approve', $purchaseRequest) }}" method="POST">@csrf
                        <button class="btn btn-sm btn-success" style="border-radius:10px;">Approve</button>
                    </form>
                    <form action="{{ route('purchase-requests.reject', $purchaseRequest) }}" method="POST">@csrf
                        <button class="btn btn-sm btn-danger" style="border-radius:10px;">Reject</button>
                    </form>
                @endif
                @if($purchaseRequest->status === 'approved')
                    <a href="{{ route('purchase-orders.create', ['pr_id' => $purchaseRequest->id]) }}" class="btn btn-sm btn-danger" style="border-radius:10px;"><i class="bi bi-cart-plus me-1"></i>Create PO</a>
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
                        <thead><tr><th>#</th><th>Part Number</th><th>Part Name</th><th>Qty</th><th>UOM</th><th>Notes</th></tr></thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->sparepart->part_number }}</td>
                                <td>{{ $item->sparepart->part_name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->sparepart->uom }}</td>
                                <td>{{ $item->notes ?? '-' }}</td>
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
