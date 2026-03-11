@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
{{-- ============ ROW 1: KPI CARDS ============ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-blue p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="bi bi-truck"></i></div>
                <span class="kpi-badge up"><i class="bi bi-check-circle me-1"></i>{{ $available }}</span>
            </div>
            <div class="kpi-label">Total Unit Fleet</div>
            <div class="kpi-value">{{ $totalUnits }}</div>
            <div class="d-flex gap-3 mt-2" style="font-size:.78rem;">
                <span class="text-success"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i> {{ $available }} Ready</span>
                <span class="text-danger"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i> {{ $underRepair }} Repair</span>
                <span class="text-warning"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i> {{ $standby }} Standby</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-red p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(220,38,38,.12);color:#dc2626;"><i class="bi bi-tools"></i></div>
                <span class="kpi-badge warn">{{ $openWO }} Open</span>
            </div>
            <div class="kpi-label">Work Orders</div>
            <div class="kpi-value">{{ $openWO + $completedWO }}</div>
            <div class="d-flex gap-3 mt-2" style="font-size:.78rem;">
                <span class="text-warning"><i class="bi bi-clock"></i> {{ $openWO }} Active</span>
                <span class="text-success"><i class="bi bi-check2-all"></i> {{ $completedWO }} Done</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-orange p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="bi bi-cart-check"></i></div>
                @if($openPR + $openPO > 0)
                <span class="kpi-badge warn">{{ $openPR + $openPO }} Pending</span>
                @endif
            </div>
            <div class="kpi-label">Procurement</div>
            <div class="kpi-value" style="font-size:1.6rem;">{{ $openPR }} PR / {{ $openPO }} PO</div>
            <div class="kpi-note">Open procurement documents</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="erp-card kpi-card card-accent-green p-3">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="bi bi-box-seam"></i></div>
                @if($lowStockCount > 0)
                <span class="kpi-badge" style="background:#fee2e2;color:#b91c1c;">{{ $lowStockCount }} Low</span>
                @else
                <span class="kpi-badge up">OK</span>
                @endif
            </div>
            <div class="kpi-label">Warehouse Stock</div>
            <div class="kpi-value">{{ $totalSpareparts }}</div>
            <div class="kpi-note">{{ $lowStockCount }} items below minimum</div>
        </div>
    </div>
</div>

{{-- ============ ROW 2: TOTAL COST + COST BREAKDOWN ============ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="erp-card p-3 summary-highlight" style="min-height:100%;">
            <div class="kpi-label mb-1"><i class="bi bi-cash-stack me-1"></i> Total Repair Cost (All Time)</div>
            <div style="font-size:1.8rem;font-weight:800;color:#dc2626;">IDR {{ number_format($totalRepairCostAll, 0, ',', '.') }}</div>
            <hr style="border-color:rgba(220,38,38,.15);">
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                <span class="text-muted"><i class="bi bi-gear me-1"></i>Sparepart</span>
                <strong>{{ number_format($costBreakdown['sparepart'], 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                <span class="text-muted"><i class="bi bi-person me-1"></i>Labor</span>
                <strong>{{ number_format($costBreakdown['labor'], 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                <span class="text-muted"><i class="bi bi-building me-1"></i>Vendor</span>
                <strong>{{ number_format($costBreakdown['vendor'], 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between" style="font-size:.85rem;">
                <span class="text-muted"><i class="bi bi-droplet me-1"></i>Consumable</span>
                <strong>{{ number_format($costBreakdown['consumable'], 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="erp-card" style="min-height:100%;">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-pie-chart me-2"></i>Cost Breakdown</div>
            </div>
            <div class="erp-card-body d-flex align-items-center justify-content-center">
                <div style="width:220px;height:220px;position:relative;">
                    <canvas id="costBreakdownChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="erp-card" style="min-height:100%;">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-speedometer2 me-2"></i>Unit Status</div>
            </div>
            <div class="erp-card-body d-flex align-items-center justify-content-center">
                <div style="width:220px;height:220px;position:relative;">
                    <canvas id="statusChart"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                        <div style="font-size:1.8rem;font-weight:800;line-height:1;">{{ $totalUnits }}</div>
                        <div style="font-size:.7rem;color:var(--text-muted);">UNITS</div>
                    </div>
                </div>
            </div>
            <div class="px-3 pb-3">
                <div class="mini-legend">
                    <div class="legend-item"><span class="legend-dot dot-green"></span> Available ({{ $available }})</div>
                    <div class="legend-item"><span class="legend-dot dot-red"></span> Repair ({{ $underRepair }})</div>
                    <div class="legend-item"><span class="legend-dot dot-yellow"></span> Standby ({{ $standby }})</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ ROW 3: AVAILABILITY TREND + MONTHLY COST ============ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="section-title"><i class="bi bi-graph-up me-2"></i>Availability Trend</div>
                    <div class="section-subtitle">Daily average availability %</div>
                </div>
                <span class="chart-chip"><i class="bi bi-calendar3 me-1"></i>All Period</span>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-lg"><canvas id="availChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-bar-chart me-2"></i>Monthly Cost Trend</div>
                <div class="section-subtitle">Repair cost per month</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-lg"><canvas id="monthlyCostChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- ============ ROW 4: TOP COST PER UNIT + TOP SPAREPART ============ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-truck me-2"></i>Repair Cost per Unit</div>
                <div class="section-subtitle">Top 10 highest cost units</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-md"><canvas id="costUnitChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-gear me-2"></i>Top Sparepart Usage Cost</div>
                <div class="section-subtitle">Most expensive parts consumed</div>
            </div>
            <div class="erp-card-body">
                <div class="chart-box chart-md"><canvas id="sparepartCostChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- ============ ROW 5: WO TYPE + UNITS ATTENTION + LOW STOCK ============ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-wrench me-2"></i>WO by Type</div>
            </div>
            <div class="erp-card-body d-flex align-items-center justify-content-center">
                <div style="width:180px;height:180px;"><canvas id="woTypeChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Units Needing Attention</div>
                <span class="badge badge-soft-danger" style="border-radius:999px;">{{ $unitsAttention->count() }}</span>
            </div>
            <div class="erp-card-body" style="max-height:280px;overflow-y:auto;">
                @forelse($unitsAttention as $ua)
                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <a href="{{ route('units.show', $ua) }}" style="font-weight:700;">{{ $ua->unit_code }}</a>
                        <div class="text-muted" style="font-size:.78rem;">{{ $ua->unit_model }} &middot; {{ $ua->category->name ?? '' }}</div>
                    </div>
                    @include('components.status-badge', ['status' => $ua->current_status])
                </div>
                @empty
                <div class="text-center text-muted py-4"><i class="bi bi-check-circle fs-3 text-success d-block mb-2"></i>All units operational</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title"><i class="bi bi-box-seam text-danger me-2"></i>Low Stock Alert</div>
                <span class="badge badge-soft-warning" style="border-radius:999px;">{{ $lowStockCount }} items</span>
            </div>
            <div class="erp-card-body" style="max-height:280px;overflow-y:auto;">
                @forelse($lowStockParts as $lsp)
                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <div style="font-weight:600;">{{ $lsp->part_number }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $lsp->part_name }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-weight:700;color:#dc2626;">{{ $lsp->stock_on_hand }} {{ $lsp->uom }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Min: {{ $lsp->minimum_stock }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4"><i class="bi bi-check-circle fs-3 text-success d-block mb-2"></i>All stock levels OK</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ============ ROW 6: RECENT WORK ORDERS ============ --}}
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title"><i class="bi bi-clock-history me-2"></i>Recent Work Orders</div>
        <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Technician</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentWOs as $wo)
                    <tr>
                        <td><a href="{{ route('work-orders.show', $wo) }}" style="font-weight:700;">{{ $wo->wo_number }}</a></td>
                        <td>{{ $wo->unit->unit_code ?? '-' }} <span class="text-muted" style="font-size:.75rem;">{{ $wo->unit->unit_model ?? '' }}</span></td>
                        <td><span class="badge {{ $wo->maintenance_type === 'corrective' ? 'badge-soft-danger' : ($wo->maintenance_type === 'preventive' ? 'badge-soft-success' : 'badge-soft-info') }}" style="border-radius:999px;">{{ ucfirst($wo->maintenance_type) }}</span></td>
                        <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $wo->status])</td>
                        <td>{{ $wo->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No work orders yet.</td></tr>
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
    const fmt = v => 'IDR ' + new Intl.NumberFormat('id-ID').format(v);

    // === Availability Trend ===
    const availData = @json($availTrend);
    if (document.getElementById('availChart') && availData.length > 0) {
        new Chart(document.getElementById('availChart'), {
            type: 'line',
            data: {
                labels: availData.map(d => {
                    const dt = new Date(d.date);
                    return dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short'});
                }),
                datasets: [{
                    label: 'Avg Availability %',
                    data: availData.map(d => parseFloat(d.avg_avail)),
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220,38,38,.06)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#dc2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.y.toFixed(1) + '%' } }
                },
                scales: {
                    y: { min: 0, max: 100, ticks: { callback: v => v + '%', stepSize: 25 }, grid: { color: 'rgba(0,0,0,.04)' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 15 } }
                }
            }
        });
    }

    // === Unit Status Doughnut ===
    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Under Repair', 'Standby'],
                datasets: [{
                    data: [{{ $available }}, {{ $underRepair }}, {{ $standby }}],
                    backgroundColor: ['#10b981','#ef4444','#f59e0b'],
                    borderWidth: 0,
                    spacing: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' units' } } }
            }
        });
    }

    // === Cost Breakdown Doughnut ===
    const cb = @json($costBreakdown);
    if (document.getElementById('costBreakdownChart')) {
        new Chart(document.getElementById('costBreakdownChart'), {
            type: 'doughnut',
            data: {
                labels: ['Sparepart', 'Labor', 'Vendor', 'Consumable'],
                datasets: [{
                    data: [parseFloat(cb.sparepart), parseFloat(cb.labor), parseFloat(cb.vendor), parseFloat(cb.consumable)],
                    backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6'],
                    borderWidth: 0,
                    spacing: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': ' + fmt(ctx.parsed) } }
                }
            }
        });
    }

    // === Monthly Cost Trend ===
    const mct = @json($monthlyCostTrend);
    if (document.getElementById('monthlyCostChart') && mct.length > 0) {
        new Chart(document.getElementById('monthlyCostChart'), {
            type: 'bar',
            data: {
                labels: mct.map(d => d.month),
                datasets: [
                    { label: 'Sparepart', data: mct.map(d => parseFloat(d.sparepart)), backgroundColor: '#3b82f6', borderRadius: 6, barPercentage: 0.7 },
                    { label: 'Labor', data: mct.map(d => parseFloat(d.labor)), backgroundColor: '#10b981', borderRadius: 6, barPercentage: 0.7 },
                    { label: 'Vendor', data: mct.map(d => parseFloat(d.vendor)), backgroundColor: '#f59e0b', borderRadius: 6, barPercentage: 0.7 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + fmt(ctx.parsed.y) } }
                },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, ticks: { callback: v => (v / 1000000).toFixed(0) + 'M' }, grid: { color: 'rgba(0,0,0,.04)' } }
                }
            }
        });
    }

    // === Cost per Unit ===
    const costData = @json($costPerUnit);
    if (document.getElementById('costUnitChart') && costData.length > 0) {
        new Chart(document.getElementById('costUnitChart'), {
            type: 'bar',
            data: {
                labels: costData.map(d => d.unit ? d.unit.unit_code : 'N/A'),
                datasets: [{
                    label: 'Total Cost',
                    data: costData.map(d => parseFloat(d.total)),
                    backgroundColor: costData.map((_, i) => {
                        const colors = ['#dc2626','#ef4444','#f87171','#fca5a5','#fecaca','#fee2e2','#fef2f2','#fff5f5','#fff8f8','#fffafa'];
                        return colors[i] || '#fecaca';
                    }),
                    borderRadius: 8,
                    barPercentage: 0.7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => fmt(ctx.parsed.x) } } },
                scales: {
                    x: { ticks: { callback: v => (v / 1000000).toFixed(0) + 'M' }, grid: { color: 'rgba(0,0,0,.04)' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // === Top Sparepart Cost ===
    const spData = @json($topSpareparts);
    if (document.getElementById('sparepartCostChart') && spData.length > 0) {
        new Chart(document.getElementById('sparepartCostChart'), {
            type: 'bar',
            data: {
                labels: spData.map(d => d.part_name.length > 25 ? d.part_name.substring(0, 25) + '...' : d.part_name),
                datasets: [{
                    label: 'Cost',
                    data: spData.map(d => parseFloat(d.total)),
                    backgroundColor: spData.map((_, i) => {
                        const colors = ['#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#dbeafe','#eff6ff','#f5f9ff','#f8fbff'];
                        return colors[i] || '#dbeafe';
                    }),
                    borderRadius: 8,
                    barPercentage: 0.7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => fmt(ctx.parsed.x) } } },
                scales: {
                    x: { ticks: { callback: v => (v / 1000000).toFixed(0) + 'M' }, grid: { color: 'rgba(0,0,0,.04)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // === WO by Type ===
    const woType = @json($woByType);
    if (document.getElementById('woTypeChart') && woType.length > 0) {
        const typeColors = { corrective: '#ef4444', preventive: '#10b981', predictive: '#3b82f6' };
        new Chart(document.getElementById('woTypeChart'), {
            type: 'doughnut',
            data: {
                labels: woType.map(d => d.maintenance_type.charAt(0).toUpperCase() + d.maintenance_type.slice(1)),
                datasets: [{
                    data: woType.map(d => d.total),
                    backgroundColor: woType.map(d => typeColors[d.maintenance_type] || '#94a3b8'),
                    borderWidth: 0, spacing: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '60%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } } }
            }
        });
    }
});
</script>
@endpush
