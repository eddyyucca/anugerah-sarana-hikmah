@extends('layouts.app')
@section('page-title', 'Warehouse Transfer')
@section('breadcrumb')<li class="breadcrumb-item active">Warehouse Transfer</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-arrow-left-right me-2"></i>Transfer List</div>
        <a href="{{ route('warehouse-transfer.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i>New Transfer</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Transfer number..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><select name="status" class="form-select form-select-sm" style="border-radius:10px;"><option value="">All</option>@foreach(['draft','posted'] as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive"><table class="table table-modern mb-0">
            <thead><tr><th>Number</th><th>From</th><th>To</th><th>Date</th><th>Items</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($transfers as $t)
                <tr>
                    <td><a href="{{ route('warehouse-transfer.show', $t) }}" style="font-weight:700;">{{ $t->transfer_number }}</a></td>
                    <td>{{ $t->fromLocation->name ?? '-' }}</td>
                    <td>{{ $t->toLocation->name ?? '-' }}</td>
                    <td>{{ $t->transfer_date->format('d M Y') }}</td>
                    <td>{{ $t->items_count }}</td>
                    <td>@include('components.status-badge', ['status' => $t->status])</td>
                    <td><a href="{{ route('warehouse-transfer.show', $t) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty<tr><td colspan="7" class="text-center text-muted py-4">No transfers.</td></tr>@endforelse
            </tbody>
        </table></div>
        <div class="mt-3">{{ $transfers->links() }}</div>
    </div>
</div>
@endsection
