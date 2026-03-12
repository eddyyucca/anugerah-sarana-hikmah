@extends('layouts.app')
@section('page-title', 'Downtime Analysis')
@section('breadcrumb')<li class="breadcrumb-item active">Downtime Analysis</li>@endsection

@section('content')
{{-- Filter --}}
<div class="erp-card mb-3">
    <div class="erp-card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size:.8rem;">Unit</label>
                <select name="unit_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Units</option>
                    @foreach($units as $u)<option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }} - {{ $u->unit_model }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-danger btn-sm" style="border-radius:10px;"><i class="bi bi-funnel me-1"></i>Filter</button> <a href="{{ route('downtime.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a></div>
        </form>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl">
        <div class="erp-card kpi-card card-accent-red p-3">
            <div class="kpi-label">Total Downtime</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ number_format($totalDowntime, 1) }}<span style="font-size:.8rem;color:var(--text-muted);"> hrs</span></div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="erp-card kpi-card card-accent-blue p-3">
            <div class="kpi-label">Total WO</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ $totalWO }}</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="erp-card kpi-card card-accent-orange p-3">
            <div class="kpi-label">Avg MTTR</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ $avgMTTR }}<span style="font-size:.8rem;color:var(--text-muted);"> hrs</span></div>
            <div class="kpi-note">Mean Time To Repair</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="erp-card kpi-card card-accent-green p-3">
            <div class="kpi-label">Avg MTBF</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ $avgMTBF }}<span style="font-size:.8rem;color:var(--text-muted);"> hrs</span></div>
            <div class="kpi-note">Mean Time Between Failure</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="erp-card kpi-card p-3 {{ $overallAvail >= 85 ? 'card-accent-green' : 'card-accent-red' }}">
            <div class="kpi-label">Overall Availability</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ $overallAvail }}%</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-graph-down me-2"></i>Downtime Trend (Monthly)</div></div>
            <div class="erp-card-body"><div class="chart-box chart-lg"><canvas id="dtTrendChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-wrench me-2"></i>WO by Type</div></div>
            <div class="erp-card-body d-flex align-items-center justify-content-center"><div style="width:200px;height:200px;"><canvas id="woTypeChart"></canvas></div></div>
        </div>
    </div>
</div>

{{-- MTBF / MTTR Table --}}
<div class="erp-card mb-4">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-speedometer2 me-2"></i>MTBF & MTTR per Unit</div></div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Unit</th><th>Model</th><th>Failures</th><th>Total Downtime (hrs)</th><th>MTTR (hrs)</th><th>MTBF (hrs)</th><th>Reliability</th></tr></thead>
                <tbody>
                    @forelse($mtbfData as $m)
                    <tr>
                        <td><strong>{{ $m->unit_code }}</strong></td>
                        <td>{{ $m->unit_model }}</td>
                        <td>{{ $m->total_failures }}</td>
                        <td>{{ $m->total_downtime }}</td>
                        <td><span class="badge badge-soft-warning" style="border-radius:999px;">{{ $m->mttr }} hrs</span></td>
                        <td><span class="badge badge-soft-info" style="border-radius:999px;">{{ $m->mtbf }} hrs</span></td>
                        <td>
                            @php $rel = $m->mtbf > 0 ? round(($m->mtbf / ($m->mtbf + $m->mttr)) * 100, 1) : 0; @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-modern flex-grow-1" style="height:8px;"><div class="progress-bar" style="width:{{ $rel }}%;background:{{ $rel >= 80 ? '#10b981' : ($rel >= 60 ? '#f59e0b' : '#ef4444') }};"></div></div>
                                <span style="font-size:.82rem;font-weight:700;">{{ $rel }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty<tr><td colspan="7" class="text-center text-muted py-4">No failure data.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Top Breakdown Reasons --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-exclamation-triangle me-2"></i>Top Breakdown Reasons</div></div>
            <div class="erp-card-body"><div class="chart-box chart-md"><canvas id="breakdownChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title"><i class="bi bi-list-ol me-2"></i>Breakdown Detail</div></div>
            <div class="erp-card-body" style="max-height:340px;overflow-y:auto;">
                @forelse($topBreakdowns as $i => $bd)
                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <span class="badge bg-dark me-1" style="border-radius:999px;width:24px;">{{ $i + 1 }}</span>
                        <span style="font-size:.88rem;">{{ Str::limit($bd->complaint, 60) }}</span>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div style="font-weight:700;">{{ $bd->count }}x</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ round($bd->total_downtime, 1) }} hrs</div>
                    </div>
                </div>
                @empty<div class="text-center text-muted py-3">No breakdown data.</div>@endforelse
            </div>
        </div>
    </div>
</div>

{{-- Downtime per Unit --}}
<div class="erp-card mb-4">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-truck me-2"></i>Downtime per Unit</div></div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Unit</th><th>Model</th><th>Total WO</th><th>Completed</th><th>Total Downtime (hrs)</th><th>Avg Downtime (hrs)</th><th>Downtime Bar</th></tr></thead>
                <tbody>
                    @php $maxDt = $downtimePerUnit->max('total_downtime') ?: 1; @endphp
                    @forelse($downtimePerUnit as $d)
                    <tr>
                        <td><strong>{{ $d->unit->unit_code ?? '-' }}</strong></td>
                        <td>{{ $d->unit->unit_model ?? '-' }}</td>
                        <td>{{ $d->total_wo }}</td>
                        <td>{{ $d->completed_wo }}</td>
                        <td><strong>{{ round($d->total_downtime, 1) }}</strong></td>
                        <td>{{ round($d->avg_downtime, 1) }}</td>
                        <td>
                            <div class="progress-modern" style="height:10px;"><div class="progress-bar" style="width:{{ ($d->total_downtime / $maxDt) * 100 }}%;"></div></div>
                        </td>
                    </tr>
                    @empty<tr><td colspan="7" class="text-center text-muted py-4">No data.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Unit Availability --}}
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-check2-square me-2"></i>Unit Availability</div></div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Unit</th><th>Model</th><th>Days Tracked</th><th>Scheduled (hrs)</th><th>Downtime (hrs)</th><th>Availability</th></tr></thead>
                <tbody>
                    @forelse($availSummary as $a)
                    <tr>
                        <td><strong>{{ $a->unit->unit_code ?? '-' }}</strong></td>
                        <td>{{ $a->unit->unit_model ?? '-' }}</td>
                        <td>{{ $a->days_counted }}</td>
                        <td>{{ round($a->total_scheduled, 0) }}</td>
                        <td>{{ round($a->total_downtime, 1) }}</td>
                        <td>
                            @php $av = round($a->avg_avail, 1); @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-modern flex-grow-1" style="height:10px;"><div class="progress-bar" style="width:{{ $av }}%;background:{{ $av >= 85 ? '#10b981' : ($av >= 70 ? '#f59e0b' : '#ef4444') }};"></div></div>
                                <span style="font-size:.85rem;font-weight:700;min-width:50px;">{{ $av }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-muted py-4">No availability data.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fmt = v => new Intl.NumberFormat('id-ID').format(v);

    // Downtime Trend
    const dtTrend = @json($downtimeTrend);
    if (document.getElementById('dtTrendChart') && dtTrend.length > 0) {
        new Chart(document.getElementById('dtTrendChart'), {
            type: 'bar',
            data: {
                labels: dtTrend.map(d => d.month),
                datasets: [
                    { label: 'Downtime (hrs)', data: dtTrend.map(d => parseFloat(d.total)), backgroundColor: 'rgba(220,38,38,.7)', borderRadius: 8, yAxisID: 'y', barPercentage: 0.5 },
                    { label: 'WO Count', data: dtTrend.map(d => d.wo_count), type: 'line', borderColor: '#3b82f6', pointBackgroundColor: '#3b82f6', tension: 0.3, yAxisID: 'y1', borderWidth: 2 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                scales: {
                    y: { position: 'left', title: { display: true, text: 'Hours' }, grid: { color: 'rgba(0,0,0,.04)' } },
                    y1: { position: 'right', title: { display: true, text: 'Count' }, grid: { drawOnChartArea: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // WO Type
    const woType = @json($woByType);
    if (document.getElementById('woTypeChart') && woType.length > 0) {
        new Chart(document.getElementById('woTypeChart'), {
            type: 'doughnut',
            data: {
                labels: woType.map(d => d.maintenance_type.charAt(0).toUpperCase() + d.maintenance_type.slice(1)),
                datasets: [{ data: woType.map(d => d.total), backgroundColor: ['#ef4444','#10b981','#3b82f6'], borderWidth: 0, spacing: 3 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } }
        });
    }

    // Top Breakdown
    const bd = @json($topBreakdowns);
    if (document.getElementById('breakdownChart') && bd.length > 0) {
        new Chart(document.getElementById('breakdownChart'), {
            type: 'bar',
            data: {
                labels: bd.map(d => d.complaint.length > 30 ? d.complaint.substring(0, 30) + '...' : d.complaint),
                datasets: [{ label: 'Count', data: bd.map(d => d.count), backgroundColor: 'rgba(239,68,68,.7)', borderRadius: 6, barPercentage: 0.6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { grid: { color: 'rgba(0,0,0,.04)' } }, y: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
        });
    }
});
</script>
@endpush
