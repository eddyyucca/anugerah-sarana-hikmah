@extends('layouts.app')
@section('page-title', 'Reports')
@section('breadcrumb')<li class="breadcrumb-item active">Reports</li>@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('reports.availability') }}" class="text-decoration-none">
            <div class="erp-card p-3 card-accent-green">
                <div class="kpi-icon mb-3" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="bi bi-speedometer2"></i></div>
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-main);">Unit Availability</div>
                <div class="text-muted" style="font-size:.85rem;">Availability percentage per unit with downtime analysis</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.repair-cost') }}" class="text-decoration-none">
            <div class="erp-card p-3 card-accent-red">
                <div class="kpi-icon mb-3" style="background:rgba(220,38,38,.12);color:#dc2626;"><i class="bi bi-cash-stack"></i></div>
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-main);">Repair Cost</div>
                <div class="text-muted" style="font-size:.85rem;">Cost breakdown per unit and work order</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.stock-movement') }}" class="text-decoration-none">
            <div class="erp-card p-3 card-accent-blue">
                <div class="kpi-icon mb-3" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="bi bi-arrow-left-right"></i></div>
                <div style="font-weight:700;font-size:1.1rem;color:var(--text-main);">Stock Movement</div>
                <div class="text-muted" style="font-size:.85rem;">Incoming and outgoing stock transactions</div>
            </div>
        </a>
    </div>
</div>
@endsection
