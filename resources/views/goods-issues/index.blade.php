@extends('layouts.app')
@section('page-title', 'Goods Issues')
@section('breadcrumb')<li class="breadcrumb-item active">Goods Issues</li>@endsection

@section('content')
<x-card>
    <x-slot:header>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 w-100">
            <div class="section-title">Goods Issue List</div>
            <a href="{{ route('goods-issues.create') }}" class="btn btn-danger btn-sm"><i class="bi bi-plus-lg me-1"></i> Create GI</a>
        </div>
    </x-slot:header>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="GI Number..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm tom-select">
                <option value="">All Status</option>
                @foreach(['draft','posted','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-auto">
            <x-button type="submit" variant="outline-secondary" size="sm">Filter</x-button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-modern mb-0">
            <thead><tr><th>GI Number</th><th>WO Ref</th><th>Date</th><th>Items</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($gis as $gi)
                <tr>
                    <td><a href="{{ route('goods-issues.show', $gi) }}"><strong>{{ $gi->gi_number }}</strong></a></td>
                    <td>{{ $gi->workOrder->wo_number ?? '-' }}</td>
                    <td>{{ $gi->issue_date->format('d M Y') }}</td>
                    <td>{{ $gi->items_count }}</td>
                    <td>@include('components.status-badge', ['status' => $gi->status])</td>
                    <td><a href="{{ route('goods-issues.show', $gi) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No goods issues found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $gis->links() }}</div>
</x-card>
@endsection
