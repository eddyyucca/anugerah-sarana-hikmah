@extends('layouts.app')
@section('page-title', 'Work Orders')
@section('breadcrumb')<li class="breadcrumb-item active">Work Orders</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Work Order List</div>
        <a href="{{ route('work-orders.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Create WO</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="WO / Unit..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Status</option>
                    @foreach(['open','in_progress','waiting_part','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button> <a href="{{ route('work-orders.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>WO Number</th><th>Unit</th><th>Type</th><th>Technician</th><th>Status</th><th>Start</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($workOrders as $wo)
                    <tr>
                        <td><a href="{{ route('work-orders.show', $wo) }}"><strong>{{ $wo->wo_number }}</strong></a></td>
                        <td>{{ $wo->unit->unit_code ?? '-' }} <span class="text-muted" style="font-size:.8rem;">{{ $wo->unit->unit_model ?? '' }}</span></td>
                        <td>{{ ucfirst($wo->maintenance_type) }}</td>
                        <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $wo->status])</td>
                        <td>{{ $wo->start_time?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            @if(!in_array($wo->status, ['completed','cancelled']))
                            <a href="{{ route('work-orders.edit', $wo) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No work orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $workOrders->links() }}</div>
    </div>
</div>
@endsection
