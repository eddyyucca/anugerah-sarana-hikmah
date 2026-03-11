@extends('layouts.app')
@section('page-title', 'Unit Detail')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">{{ $unit->unit_code }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:rgba(220,38,38,.12);color:#dc2626;width:56px;height:56px;font-size:1.4rem;border-radius:16px;"><i class="bi bi-truck"></i></div>
                <div>
                    <div style="font-weight:800;font-size:1.2rem;">{{ $unit->unit_code }}</div>
                    <div class="text-muted" style="font-size:.85rem;">{{ $unit->unit_model }}</div>
                </div>
            </div>
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">Type</td><td>{{ $unit->unit_type ?? '-' }}</td></tr>
                <tr><td class="text-muted">Category</td><td>{{ $unit->category->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Department</td><td>{{ $unit->department ?? '-' }}</td></tr>
                <tr><td class="text-muted">Status</td><td>@include('components.status-badge', ['status' => $unit->current_status])</td></tr>
                <tr><td class="text-muted">Hour Meter</td><td>{{ number_format($unit->hour_meter, 1) }}</td></tr>
            </table>
            <div class="mt-3">
                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-pencil me-1"></i>Edit</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Work Order History</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>WO</th><th>Type</th><th>Technician</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse($unit->workOrders->take(20) as $wo)
                            <tr>
                                <td><a href="{{ route('work-orders.show', $wo) }}">{{ $wo->wo_number }}</a></td>
                                <td>{{ ucfirst($wo->maintenance_type) }}</td>
                                <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                                <td>@include('components.status-badge', ['status' => $wo->status])</td>
                                <td>{{ $wo->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No work orders.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
