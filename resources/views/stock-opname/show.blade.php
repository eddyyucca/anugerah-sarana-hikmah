@extends('layouts.app')
@section('page-title', $stockOpname->opname_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}">Stock Opname</a></li><li class="breadcrumb-item active">{{ $stockOpname->opname_number }}</li>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between mb-3">
                <div><div style="font-weight:800;font-size:1.1rem;">{{ $stockOpname->opname_number }}</div><div class="text-muted" style="font-size:.82rem;">{{ $stockOpname->opname_date->format('d M Y') }}</div></div>
                @include('components.status-badge', ['status' => $stockOpname->status == 'completed' ? 'completed' : 'draft'])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Location</td><td>{{ $stockOpname->warehouseLocation->name ?? 'All' }}</td></tr>
                <tr><td class="text-muted">Conducted</td><td>{{ $stockOpname->conductor->name ?? '-' }}</td></tr>
                @if($stockOpname->approver)<tr><td class="text-muted">Approved</td><td>{{ $stockOpname->approver->name }}</td></tr>@endif
            </table>
            @if($stockOpname->status === 'draft')
            <form action="{{ route('stock-opname.approve', $stockOpname) }}" method="POST">@csrf
                <button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Approve? Stock akan disesuaikan.')"><i class="bi bi-check-lg me-1"></i>Approve & Adjust Stock</button>
            </form>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Opname Items</div></div>
            <div class="erp-card-body"><div class="table-responsive"><table class="table table-modern mb-0">
                <thead><tr><th>Part Number</th><th>Name</th><th>System</th><th>Physical</th><th>Diff</th><th>Notes</th></tr></thead>
                <tbody>
                    @foreach($stockOpname->items as $item)
                    <tr class="{{ $item->difference != 0 ? ($item->difference > 0 ? 'table-success' : 'table-danger') : '' }}">
                        <td>{{ $item->sparepart->part_number }}</td>
                        <td>{{ $item->sparepart->part_name }}</td>
                        <td>{{ $item->system_qty }}</td>
                        <td><strong>{{ $item->physical_qty }}</strong></td>
                        <td><strong class="{{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : '') }}">{{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}</strong></td>
                        <td class="text-muted">{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div></div>
        </div>
    </div>
</div>
@endsection
