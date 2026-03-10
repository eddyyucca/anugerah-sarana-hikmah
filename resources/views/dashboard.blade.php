@extends('layouts.app')

@php
    $title = 'Workshop ERP Dashboard';
    $pageTitle = 'Workshop Dashboard';
    $pageSubtitle = 'Overview of unit availability, repair activity, procurement, and maintenance cost.';
    $maxCost = collect($costBreakdown)->max('value');
@endphp

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="erp-card kpi-card">
                <div class="erp-card-body">
                    <div class="kpi-label">Total Unit</div>
                    <div class="kpi-value">{{ number_format($summary['units_total']) }}</div>
                    <div class="kpi-note">Registered operational units</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="erp-card kpi-card">
                <div class="erp-card-body">
                    <div class="kpi-label">Available Unit</div>
                    <div class="kpi-value">{{ number_format($summary['units_available']) }}</div>
                    <div class="kpi-note">Ready for operation</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="erp-card kpi-card">
                <div class="erp-card-body">
                    <div class="kpi-label">Under Repair</div>
                    <div class="kpi-value">{{ number_format($summary['units_repair']) }}</div>
                    <div class="kpi-note">Units requiring maintenance</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="erp-card kpi-card">
                <div class="erp-card-body">
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

        <div class="col-12 col-xl-4">
            <div class="erp-card h-100">
                <div class="erp-card-header">
                    <h3 class="section-title">Quick Summary</h3>
                    <div class="section-subtitle">Dummy overview</div>
                </div>

                <div class="erp-card-body">
                    <div class="vstack gap-3">
                        <div class="summary-box">
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
                            Biaya repair terbesar masih berasal dari sparepart. Bagian ini cocok nanti dikembangkan ke monitoring cost per unit, cost center, dan approval pembelian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection