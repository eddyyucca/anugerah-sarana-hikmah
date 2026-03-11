@extends('layouts.app')
@section('page-title', $goodsIssue->gi_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-issues.index') }}">Goods Issues</a></li><li class="breadcrumb-item active">{{ $goodsIssue->gi_number }}</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><div style="font-weight:800;font-size:1.2rem;">{{ $goodsIssue->gi_number }}</div><div class="text-muted" style="font-size:.85rem;">{{ $goodsIssue->issue_date->format('d M Y') }}</div></div>
                @include('components.status-badge', ['status' => $goodsIssue->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">WO Ref</td><td>@if($goodsIssue->workOrder)<a href="{{ route('work-orders.show', $goodsIssue->workOrder) }}">{{ $goodsIssue->workOrder->wo_number }}</a>@else - @endif</td></tr>
                <tr><td class="text-muted">Total Value</td><td><strong>IDR {{ number_format($goodsIssue->items->sum('total_price'), 0, ',', '.') }}</strong></td></tr>
                <tr><td class="text-muted">Remarks</td><td>{{ $goodsIssue->remarks ?? '-' }}</td></tr>
                @if($goodsIssue->postedByUser)<tr><td class="text-muted">Posted By</td><td>{{ $goodsIssue->postedByUser->name }}</td></tr>@endif
                @if($goodsIssue->posted_at)<tr><td class="text-muted">Posted At</td><td>{{ $goodsIssue->posted_at->format('d M Y H:i') }}</td></tr>@endif
            </table>
            @if($goodsIssue->status === 'draft')
            <form action="{{ route('goods-issues.post', $goodsIssue) }}" method="POST">@csrf
                <button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Post this GI? Stock will decrease.')"><i class="bi bi-check-lg me-1"></i>Post GI</button>
            </form>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Items Issued</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>#</th><th>Part Number</th><th>Part Name</th><th>Location</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach($goodsIssue->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->sparepart->part_number }}</td>
                                <td>{{ $item->sparepart->part_name }}</td>
                                <td>{{ $item->warehouseLocation->name ?? '-' }}</td>
                                <td>{{ $item->qty_issued }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><td colspan="6" class="text-end"><strong>Total</strong></td><td><strong>{{ number_format($goodsIssue->items->sum('total_price'), 0, ',', '.') }}</strong></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
