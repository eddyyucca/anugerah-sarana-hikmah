@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-blue p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="bi bi-truck"></i></div>
                <span class="kpi-badge up">Active</span>
            </div>
            <div class="kpi-label">Total Units</div>
            <div class="kpi-value">{{ $totalUnits }}</div>
            <div class="kpi-note">{{ $available }} available</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-red p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(220,38,38,.12);color:#dc2626;"><i class="bi bi-tools"></i></div>
                <span class="kpi-badge warn">{{ $underRepair }}</span>
            </div>
            <div class="kpi-label">Under Repair</div>
            <div class="kpi-value">{{ $underRepair }}</div>
            <div class="kpi-note">{{ $openWO }} open work orders</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-orange p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="bi bi-file-earmark-text"></i></div>
            </div>
            <div class="kpi-label">Open PR / PO</div>
            <div class="kpi-value">{{ $openPR }} / {{ $openPO }}</div>
            <div class="kpi-note">Pending procurement</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-green p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="bi bi-cash-stack"></i></div>
            </div>
            <div class="kpi-label">Monthly Repair Cost</div>
            <div class="kpi-value">{{ number_format($monthlyRepairCost, 0, ',', '.') }}</div>
            <div class="kpi-note">IDR this month</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="section-title">Availability Trend</div>
                    <div class="section-subtitle">Last 30 days average</div>
                </div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-lg">
                    <canvas id="availChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title">Unit Status</div>
                <div class="section-subtitle">Current distribution</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-lg">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mini-legend mt-3">
                    <div class="legend-item"><span class="legend-dot dot-green"></span> Available</div>
                    <div class="legend-item"><span class="legend-dot dot-red"></span> Under Repair</div>
                    <div class="legend-item"><span class="legend-dot dot-yellow"></span> Standby</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title">Repair Cost per Unit</div>
                <div class="section-subtitle">This month - Top 10</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-md">
                    <canvas id="costUnitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title">Top Sparepart Cost</div>
                <div class="section-subtitle">This month - Top 10</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-md">
                    <canvas id="sparepartCostChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Work Orders --}}
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title">Recent Work Orders</div>
    </div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Unit</th>
                        <th>Technician</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentWOs as $wo)
                    <tr>
                        <td><a href="{{ route('work-orders.show', $wo) }}">{{ $wo->wo_number }}</a></td>
                        <td>{{ $wo->unit->unit_code ?? '-' }}</td>
                        <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $wo->status])</td>
                        <td>{{ $wo->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No recent work orders.</td></tr>
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
    // Availability Trend
    const availData = @json($availTrend);
    new Chart(document.getElementById('availChart'), {
        type: 'line',
        data: {
            labels: availData.map(d => d.date),
            datasets: [{
                label: 'Avg Availability %',
                data: availData.map(d => parseFloat(d.avg_avail)),
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220,38,38,.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } } }
        }
    });

    // Status Doughnut
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Under Repair', 'Standby'],
            datasets: [{ data: [{{ $available }}, {{ $underRepair }}, {{ $standby }}], backgroundColor: ['#10b981','#ef4444','#f59e0b'] }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '65%',
        }
    });

    // Cost per Unit
    const costData = @json($costPerUnit);
    new Chart(document.getElementById('costUnitChart'), {
        type: 'bar',
        data: {
            labels: costData.map(d => d.unit ? d.unit.unit_code : 'N/A'),
            datasets: [{ label: 'Cost', data: costData.map(d => parseFloat(d.total)), backgroundColor: 'rgba(220,38,38,.7)', borderRadius: 8 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
        }
    });

    // Top Sparepart Cost
    const spData = @json($topSpareparts);
    new Chart(document.getElementById('sparepartCostChart'), {
        type: 'bar',
        data: {
            labels: spData.map(d => d.part_name),
            datasets: [{ label: 'Cost', data: spData.map(d => parseFloat(d.total)), backgroundColor: 'rgba(59,130,246,.7)', borderRadius: 8 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
        }
    });
});
</script>
@endpush
