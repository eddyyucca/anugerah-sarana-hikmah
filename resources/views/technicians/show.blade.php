@extends('layouts.app')
@section('page-title', 'Technician Detail')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('technicians.index') }}">Technicians</a></li><li class="breadcrumb-item active">{{ $technician->technician_code }}</li>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div style="font-weight:800;font-size:1.2rem;" class="mb-2">{{ $technician->technician_code }}</div>
            <table class="table table-sm"><tr><td class="text-muted">Name</td><td>{{ $technician->technician_name }}</td></tr><tr><td class="text-muted">Skill</td><td>{{ $technician->skill ?? '-' }}</td></tr><tr><td class="text-muted">Phone</td><td>{{ $technician->phone ?? '-' }}</td></tr></table>
            <a href="{{ route('technicians.edit', $technician) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-pencil me-1"></i>Edit</a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card"><div class="erp-card-header"><div class="section-title">Work Orders</div></div><div class="erp-card-body">
            <div class="table-responsive"><table class="table table-modern mb-0"><thead><tr><th>WO</th><th>Unit</th><th>Status</th><th>Date</th></tr></thead><tbody>
            @forelse($technician->workOrders->take(20) as $wo)<tr><td><a href="{{ route('work-orders.show', $wo) }}">{{ $wo->wo_number }}</a></td><td>{{ $wo->unit->unit_code ?? '-' }}</td><td>@include('components.status-badge', ['status' => $wo->status])</td><td>{{ $wo->created_at->format('d M Y') }}</td></tr>
            @empty<tr><td colspan="4" class="text-center text-muted py-3">No work orders.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
</div>
@endsection
