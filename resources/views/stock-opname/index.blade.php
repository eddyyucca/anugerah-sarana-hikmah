@extends('layouts.app')
@section('page-title', 'Stock Opname')
@section('breadcrumb')<li class="breadcrumb-item active">Stock Opname</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-clipboard2-data me-2"></i>Stock Opname List</div>
        <a href="{{ route('stock-opname.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i>New Opname</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Opname number..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><select name="status" class="form-select form-select-sm" style="border-radius:10px;"><option value="">All Status</option>@foreach(['draft','completed'] as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Number</th><th>Date</th><th>Location</th><th>Items</th><th>Conducted By</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($opnames as $o)
                    <tr>
                        <td><a href="{{ route('stock-opname.show', $o) }}" style="font-weight:700;">{{ $o->opname_number }}</a></td>
                        <td>{{ $o->opname_date->format('d M Y') }}</td>
                        <td>{{ $o->warehouseLocation->name ?? 'All' }}</td>
                        <td>{{ $o->items_count }}</td>
                        <td>{{ $o->conductor->name ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $o->status == 'completed' ? 'completed' : 'draft'])</td>
                        <td><a href="{{ route('stock-opname.show', $o) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty<tr><td colspan="7" class="text-center text-muted py-4">No stock opname records.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $opnames->links() }}</div>
    </div>
</div>
@endsection
