@extends('layouts.app')

@php
    $title = 'Workshop ERP Dashboard';
    $pageTitle = 'Workshop Dashboard';
    $maxCost = collect($costBreakdown)->max('value');
@endphp

@section('content')
<div class="container-fluid px-0">

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="erp-card kpi-card card-accent-red">
                <div class="erp-card-body">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-truck"></i></span>
                        <span class="kpi-badge up">+4.2%</span>
                    </div>
                    <div class="kpi-label">Total Unit</div>
                    <div class="kpi-value">{{ number_format($summary['units_total']) }}</div>
                    <div class="kpi-note">Registered operational units</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="erp-card kpi-card card-accent-blue">
                <div class="erp-card-body">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-check2-circle"></i></span>
                        <span class="kpi-badge up">Stable</span>
                    </div>
                    <div class="kpi-label">Available Unit</div>
                    <div class="kpi-value">{{ number_format($summary['units_available']) }}</div>
                    <div class="kpi-note">Ready for operation</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="erp-card kpi-card card-accent-orange">
                <div class="erp-card-body">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-tools"></i></span>
                        <span class="kpi-badge warn">Need action</span>
                    </div>
                    <div class="kpi-label">Under Repair</div>
                    <div class="kpi-value">{{ number_format($summary['units_repair']) }}</div>
                    <div class="kpi-note">Units requiring maintenance</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="erp-card kpi-card card-accent-green">
                <div class="erp-card-body">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <span class="kpi-badge up">Monthly</span>
                    </div>
                    <div class="kpi-label">Availability</div>
                    <div class="kpi-value">{{ $summary['availability_percent'] }}%</div>
                    <div class="kpi-note">Current monthly performance</div>

                    <div class="progress progress-modern mt-3">
                        <div class="progress-bar" style="width: {{ $summary['availability_percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="erp-card h-100">
                <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3 class="section-title">Availability Trend</h3>
                        <div class="section-subtitle">Weekly movement of unit availability</div>
                    </div>
                    <span class="chart-chip">Live Summary</span>
                </div>
                <div class="erp-card-body">
                    <div class="chart-box chart-lg">
                        <canvas id="availabilityTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Unit Status Composition</h3>
                    <div class="section-subtitle">Available, repair, and standby ratio</div>
                </div>
                <div class="erp-card-body">
                    <div class="chart-box chart-md">
                        <canvas id="unitStatusDonutChart"></canvas>
                    </div>

                    <div class="mini-legend mt-4">
                        <div class="legend-item">
                            <span class="legend-dot dot-green"></span>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot dot-red"></span>
                            <span>Under Repair</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot dot-yellow"></span>
                            <span>Standby</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Repair Cost per Unit</h3>
                    <div class="section-subtitle">Highest maintenance cost by unit</div>
                </div>
                <div class="erp-card-body">
                    <div class="chart-box chart-md">
                        <canvas id="repairCostUnitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Quick Summary</h3>
                    <div class="section-subtitle">Operational snapshot</div>
                </div>
                <div class="erp-card-body">
                    <div class="vstack gap-3">
                        <div class="summary-box summary-highlight">
                            <div class="summary-label">Open PR</div>
                            <div class="summary-value">{{ number_format($summary['open_pr']) }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Open PO</div>
                            <div class="summary-value">{{ number_format($summary['open_po']) }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Monthly Repair Cost</div>
                            <div class="summary-value small">Rp {{ number_format($summary['monthly_repair_cost'], 0, ',', '.') }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Monthly Procurement Cost</div>
                            <div class="summary-value small">Rp {{ number_format($summary['monthly_procurement_cost'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-5">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Top Sparepart Cost</h3>
                    <div class="section-subtitle">Most expensive sparepart usage</div>
                </div>
                <div class="erp-card-body">
                    <div class="chart-box chart-md">
                        <canvas id="sparepartCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="erp-card h-100">
                <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3 class="section-title">Recent Activity</h3>
                        <div class="section-subtitle">Latest operational transactions</div>
                    </div>
                </div>

                <div class="erp-card-body">
                    <div class="row g-3">
                        @foreach($recentActivities as $activity)
                            <div class="col-12">
                                <div class="activity-item">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-12 col-md-3">
                                            <div class="activity-code">{{ $activity['code'] }}</div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="activity-title">{{ $activity['title'] }}</div>
                                            <div class="activity-meta">{{ $activity['time'] }}</div>
                                        </div>

                                        <div class="col-12 col-md-3 text-md-end">
                                            @php
                                                $statusClass = match($activity['status']) {
                                                    'Completed', 'Approved' => 'badge-soft-success',
                                                    'Pending' => 'badge-soft-warning',
                                                    'In Progress' => 'badge-soft-info',
                                                    default => 'badge-soft-danger',
                                                };
                                            @endphp

                                            <span class="badge rounded-pill {{ $statusClass }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Unit Status Overview</h3>
                    <div class="section-subtitle">Top monitored units</div>
                </div>

                <div class="erp-card-body">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th>Availability</th>
                                    <th>Repair Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unitStatus as $row)
                                    <tr>
                                        <td><strong>{{ $row['unit'] }}</strong></td>
                                        <td>{{ $row['model'] }}</td>
                                        <td>
                                            @if($row['status'] === 'Available')
                                                <span class="badge rounded-pill badge-soft-success">{{ $row['status'] }}</span>
                                            @elseif($row['status'] === 'Under Repair')
                                                <span class="badge rounded-pill badge-soft-danger">{{ $row['status'] }}</span>
                                            @else
                                                <span class="badge rounded-pill badge-soft-warning">{{ $row['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['availability'] }}</td>
                                        <td>Rp {{ number_format($row['cost'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Repair Cost Breakdown</h3>
                    <div class="section-subtitle">Current month</div>
                </div>

                <div class="erp-card-body">
                    @foreach($costBreakdown as $item)
                        <div class="breakdown-item">
                            <div class="breakdown-row">
                                <span>{{ $item['label'] }}</span>
                                <strong>Rp {{ number_format($item['value'], 0, ',', '.') }}</strong>
                            </div>

                            <div class="progress progress-modern">
                                <div class="progress-bar" style="width: {{ ($item['value'] / $maxCost) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="summary-box mt-4">
                        <div class="summary-label">Insight</div>
                        <div class="small text-muted">
                            Biaya repair terbesar masih berasal dari sparepart. Bagian ini cocok dikembangkan ke monitoring cost per unit, cost center, dan approval pembelian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const availabilityTrendLabels = @json($availabilityTrend['labels']);
const availabilityTrendValues = @json($availabilityTrend['values']);

const repairCostUnitLabels = @json($repairCostPerUnit['labels']);
const repairCostUnitValues = @json($repairCostPerUnit['values']);

const unitStatusLabels = @json($unitStatusChart['labels']);
const unitStatusValues = @json($unitStatusChart['values']);

const sparepartLabels = @json($sparepartTopCost['labels']);
const sparepartValues = @json($sparepartTopCost['values']);

new Chart(document.getElementById('availabilityTrendChart'), {
    type: 'line',
    data: {
        labels: availabilityTrendLabels,
        datasets: [{
            label: 'Availability %',
            data: availabilityTrendValues,
            borderColor: '#dc2626',
            backgroundColor: 'rgba(220, 38, 38, 0.12)',
            fill: true,
            tension: 0.35,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: false,
                suggestedMin: 75,
                suggestedMax: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

new Chart(document.getElementById('repairCostUnitChart'), {
    type: 'bar',
    data: {
        labels: repairCostUnitLabels,
        datasets: [{
            label: 'Repair Cost',
            data: repairCostUnitValues,
            backgroundColor: ['#dc2626', '#2563eb', '#f59e0b', '#10b981', '#8b5cf6'],
            borderRadius: 10,
            maxBarThickness: 42
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' Rp ' + context.raw.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000) + ' jt';
                    }
                }
            }
        }
    }
});

new Chart(document.getElementById('unitStatusDonutChart'), {
    type: 'doughnut',
    data: {
        labels: unitStatusLabels,
        datasets: [{
            data: unitStatusValues,
            backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: { display: false }
        }
    }
});

new Chart(document.getElementById('sparepartCostChart'), {
    type: 'bar',
    data: {
        labels: sparepartLabels,
        datasets: [{
            label: 'Top Sparepart Cost',
            data: sparepartValues,
            backgroundColor: '#111827',
            borderRadius: 10
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' Rp ' + context.raw.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000) + ' jt';
                    }
                }
            }
        }
    }
});
</script>
@endpush