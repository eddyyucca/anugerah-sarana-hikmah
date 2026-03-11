@extends('layouts.app')
@section('page-title', 'P2H Summary')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('p2h.index') }}">P2H Check</a></li><li class="breadcrumb-item active">Summary</li>@endsection

@section('content')
{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-blue p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="bi bi-clipboard-check"></i></div>
            </div>
            <div class="kpi-label">Total Inspections</div>
            <div class="kpi-value">{{ $totalChecks }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-green p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="bi bi-check-circle"></i></div>
                <span class="kpi-badge up">{{ $totalChecks > 0 ? round(($layakTotal/$totalChecks)*100) : 0 }}%</span>
            </div>
            <div class="kpi-label">Layak</div>
            <div class="kpi-value">{{ $layakTotal }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-orange p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
            <div class="kpi-label">Layak + Catatan</div>
            <div class="kpi-value">{{ $catatanTotal }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-red p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(220,38,38,.12);color:#dc2626;"><i class="bi bi-x-circle"></i></div>
                @if($tidakLayakTotal > 0)<span class="kpi-badge" style="background:#fee2e2;color:#b91c1c;">Alert</span>@endif
            </div>
            <div class="kpi-label">Tidak Layak</div>
            <div class="kpi-value">{{ $tidakLayakTotal }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="erp-card mb-4">
    <div class="erp-card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button> <a href="{{ route('p2h.summary') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a></div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Chart: Overall Status --}}
    <div class="col-lg-4">
        <div class="erp-card" style="min-height:100%;">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-pie-chart me-2"></i>P2H Status Distribution</div></div>
            <div class="erp-card-body d-flex align-items-center justify-content-center">
                <div style="width:220px;height:220px;"><canvas id="statusChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Unit Fitness Ranking --}}
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-trophy me-2"></i>Unit Fitness Ranking</div></div>
            <div class="erp-card-body">
                <div class="chart-box" style="height:300px;"><canvas id="fitnessChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- Unit Fitness Table --}}
<div class="erp-card mb-4">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-truck me-2"></i>Unit P2H Summary & Kelayakan</div></div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr>
                    <th>Unit</th><th>Model</th><th>Category</th><th>Total P2H</th>
                    <th><span class="text-success">Layak</span></th>
                    <th><span class="text-warning">Catatan</span></th>
                    <th><span class="text-danger">Tidak Layak</span></th>
                    <th>Fitness %</th><th>Last Check</th><th>Last Status</th>
                </tr></thead>
                <tbody>
                    @forelse($units as $u)
                    <tr>
                        <td><a href="{{ route('units.show', $u) }}" style="font-weight:700;">{{ $u->unit_code }}</a></td>
                        <td>{{ $u->unit_model }}</td>
                        <td>{{ $u->category->name ?? '-' }}</td>
                        <td><strong>{{ $u->total_checks }}</strong></td>
                        <td class="text-success fw-bold">{{ $u->layak_count }}</td>
                        <td class="text-warning fw-bold">{{ $u->catatan_count }}</td>
                        <td class="text-danger fw-bold">{{ $u->tidak_layak_count }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-modern flex-grow-1" style="height:8px;">
                                    <div class="progress-bar" style="width:{{ $u->fitness_percent }}%;background:{{ $u->fitness_percent >= 80 ? '#10b981' : ($u->fitness_percent >= 60 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <span style="font-size:.82rem;font-weight:700;min-width:42px;">{{ $u->fitness_percent }}%</span>
                            </div>
                        </td>
                        <td>{{ $u->last_check ? $u->last_check->check_date->format('d M Y') : '-' }}</td>
                        <td>@if($u->last_check) @include('components.p2h-status', ['status' => $u->last_check->overall_status]) @else - @endif</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No P2H data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Operator Performance Table --}}
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-people me-2"></i>Operator P2H Performance</div></div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr>
                    <th>Operator</th><th>Name</th><th>License</th><th>Total P2H</th>
                    <th><span class="text-success">Layak</span></th>
                    <th><span class="text-danger">Tidak Layak</span></th>
                    <th>Last Check</th><th>Last Unit</th><th>Last Status</th>
                </tr></thead>
                <tbody>
                    @forelse($operators as $op)
                    <tr>
                        <td><a href="{{ route('operators.show', $op) }}" style="font-weight:700;">{{ $op->operator_code }}</a></td>
                        <td>{{ $op->operator_name }}</td>
                        <td>
                            {{ $op->license_type ?? '-' }}
                            @if($op->isLicenseExpired()) <span class="badge badge-soft-danger" style="border-radius:999px;font-size:.65rem;">Expired</span> @endif
                        </td>
                        <td><strong>{{ $op->total_checks }}</strong></td>
                        <td class="text-success fw-bold">{{ $op->layak_count }}</td>
                        <td class="text-danger fw-bold">{{ $op->tidak_layak_count }}</td>
                        <td>{{ $op->last_check ? $op->last_check->check_date->format('d M Y') : '-' }}</td>
                        <td>{{ $op->last_check && $op->last_check->unit ? $op->last_check->unit->unit_code : '-' }}</td>
                        <td>@if($op->last_check) @include('components.p2h-status', ['status' => $op->last_check->overall_status]) @else - @endif</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No operator P2H data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Distribution
    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Layak', 'Layak + Catatan', 'Tidak Layak'],
                datasets: [{
                    data: [{{ $layakTotal }}, {{ $catatanTotal }}, {{ $tidakLayakTotal }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0, spacing: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } } }
            }
        });
    }

    // Fitness Chart
    const fitnessData = @json($units->values());
    if (document.getElementById('fitnessChart') && fitnessData.length > 0) {
        new Chart(document.getElementById('fitnessChart'), {
            type: 'bar',
            data: {
                labels: fitnessData.map(d => d.unit_code),
                datasets: [{
                    label: 'Fitness %',
                    data: fitnessData.map(d => d.fitness_percent),
                    backgroundColor: fitnessData.map(d => d.fitness_percent >= 80 ? '#10b981' : (d.fitness_percent >= 60 ? '#f59e0b' : '#ef4444')),
                    borderRadius: 8, barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } },
                scales: {
                    y: { min: 0, max: 100, ticks: { callback: v => v + '%', stepSize: 25 }, grid: { color: 'rgba(0,0,0,.04)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
