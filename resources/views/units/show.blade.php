@extends('layouts.app')
@section('page-title', 'Unit Detail')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">{{ $unit->unit_code }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <x-card>
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
                @if($unit->monthly_budget_limit)
                <tr><td class="text-muted">Budget/Bulan</td><td>IDR {{ number_format($unit->monthly_budget_limit, 0, ',', '.') }}</td></tr>
                @endif
            </table>

            {{-- Budget Status Card --}}
            @if($budgetStatus['has_limit'])
            <div class="mt-3 p-3 rounded-3" style="background:{{ $budgetStatus['is_over_budget'] ? 'rgba(220,38,38,.08)' : 'rgba(16,185,129,.08)' }};border:1px solid {{ $budgetStatus['is_over_budget'] ? 'rgba(220,38,38,.2)' : 'rgba(16,185,129,.2)' }};">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:.82rem;font-weight:600;">Budget Bulan Ini ({{ now()->translatedFormat('F Y') }})</span>
                    @if($budgetStatus['is_over_budget'])
                        <span class="badge badge-soft-danger" style="border-radius:999px;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Over Budget</span>
                    @else
                        <span class="badge badge-soft-success" style="border-radius:999px;"><i class="bi bi-check-circle me-1"></i>Normal</span>
                    @endif
                </div>
                <div class="progress mb-1" style="height:8px;border-radius:999px;">
                    <div class="progress-bar {{ $budgetStatus['is_over_budget'] ? 'bg-danger' : ($budgetStatus['percentage'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $budgetStatus['percentage'] }}%;border-radius:999px;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.78rem;color:#6b7280;">
                    <span>Terpakai: <strong>IDR {{ number_format($budgetStatus['used'], 0, ',', '.') }}</strong></span>
                    <span>{{ $budgetStatus['percentage'] }}%</span>
                    <span>Sisa: <strong>IDR {{ number_format($budgetStatus['remaining'], 0, ',', '.') }}</strong></span>
                </div>
                @if($budgetStatus['is_over_budget'])
                <div class="mt-2" style="font-size:.78rem;color:#dc2626;">
                    <i class="bi bi-info-circle me-1"></i>
                    WO baru untuk unit ini membutuhkan persetujuan level tertinggi.
                    @if($budgetStatus['exceeded_at'])
                    Terlampaui sejak: {{ $budgetStatus['exceeded_at']->format('d M Y H:i') }}
                    @endif
                </div>
                @endif
            </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                @if($budgetStatus['has_limit'])
                <a href="{{ route('operator-performance.index', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-outline-warning ms-2"><i class="bi bi-person-exclamation me-1"></i>Performa Operator</a>
                @endif
            </div>
        </x-card>
    </div>
    <div class="col-lg-8">
        <x-card title="Work Order History">
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
        </x-card>
    </div>
</div>
@endsection
