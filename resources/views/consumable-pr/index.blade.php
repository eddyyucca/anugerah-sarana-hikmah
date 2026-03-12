@extends('layouts.app')
@section('page-title', 'Consumable PR')
@section('breadcrumb')<li class="breadcrumb-item active">Consumable PR</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-droplet me-2"></i>Consumable Purchase Requests</div>
        <a href="{{ route('consumable-pr.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> New Consumable PR</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="PR Number..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Status</option>
                    @foreach(['draft','submitted','approved','rejected','closed'] as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>PR Number</th><th>Date</th><th>Requester</th><th>Items</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($prs as $pr)
                    <tr>
                        <td><a href="{{ route('purchase-requests.show', $pr) }}" style="font-weight:700;">{{ $pr->pr_number }}</a></td>
                        <td>{{ $pr->request_date->format('d M Y') }}</td>
                        <td>{{ $pr->requester->name ?? '-' }}</td>
                        <td>{{ $pr->items_count }}</td>
                        <td>@include('components.status-badge', ['status' => $pr->status])</td>
                        <td>
                            <a href="{{ route('purchase-requests.show', $pr) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('print.pr', $pr) }}" class="btn btn-sm btn-light" style="border-radius:8px;" target="_blank"><i class="bi bi-printer"></i></a>
                        </td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-muted py-4">No consumable PR records.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $prs->links() }}</div>
    </div>
</div>
@endsection
