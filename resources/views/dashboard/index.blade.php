@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
<style>
    /* ── KPI Cards ── */
    .kpi-card { border-radius: 14px !important; overflow: hidden; }
    .kpi-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .kpi-value { font-size: 1.9rem; font-weight: 800; line-height: 1.1; color: #1a1a2e; }
    .kpi-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .6px; color: #999; font-weight: 600; }
    .kpi-badge { font-size: .68rem; font-weight: 700; padding: 3px 9px; border-radius: 20px; white-space: nowrap; }
    .kpi-badge.up   { background: #d1fae5; color: #065f46; }
    .kpi-badge.warn { background: #fef3c7; color: #92400e; }
    .kpi-badge.down { background: #fee2e2; color: #991b1b; }

    /* ── Period Tabs ── */
    .period-nav { display: flex; gap: 4px; background: #f4f6f9; padding: 4px; border-radius: 12px; width: fit-content; }
    .period-nav .nav-link {
        font-size: .78rem; font-weight: 600; padding: .38rem 1.1rem;
        border-radius: 9px; color: #888; border: none; background: transparent;
        transition: background .15s, color .15s;
    }
    .period-nav .nav-link.active { background: #fff; color: #c0392b; box-shadow: 0 1px 6px rgba(0,0,0,.1); }

    /* ── Section ── */
    .section-title { font-size: .88rem; font-weight: 700; color: #1a1a2e; margin: 0; }
    .section-sub   { font-size: .73rem; color: #aaa; margin-top: 2px; }
    .chip { font-size: .7rem; font-weight: 600; background: #f0f0f0; color: #666; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }

    /* ── Summary rows ── */
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: .55rem 0; border-bottom: 1px solid #f5f5f5; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row .s-label { font-size: .8rem; color: #666; }
    .summary-row .s-val   { font-size: .88rem; font-weight: 700; color: #1a1a2e; }

    /* ── Activity list ── */
    .activity-row { display: flex; align-items: center; gap: 10px; padding: .5rem 0; border-bottom: 1px solid #f5f5f5; }
    .activity-row:last-child { border-bottom: none; }
    .activity-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ── Legend ── */
    .mini-legend { display: flex; gap: 14px; flex-wrap: wrap; }
    .legend-dot  { display: inline-block; width: 9px; height: 9px; border-radius: 50%; margin-right: 4px; }
    .dot-green  { background: #10b981; }
    .dot-red    { background: #ef4444; }
    .dot-yellow { background: #f59e0b; }
    .dot-blue   { background: #3b82f6; }
    .dot-purple { background: #8b5cf6; }

    /* ── Breakdown bar ── */
    .breakdown-item { margin-bottom: .75rem; }
    .breakdown-label { display: flex; justify-content: space-between; font-size: .78rem; color: #555; margin-bottom: 4px; }

    /* ── Table ── */
    .table th { background: #fafafa !important; }

    /* ── summary-highlight ── */
    .summary-highlight { border-left: 3px solid #c0392b; }

    /* ── Budget Alert Widget ── */
    .alert-widget { border-radius: 14px; border: 1.5px solid #e5e7eb; background: #fff; margin-bottom: 1.25rem; overflow: hidden; }
    .alert-widget-header { padding: .65rem 1rem; background: #fafafa; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: .5rem; flex-wrap: wrap; }
    .alert-pill { font-size: .7rem; font-weight: 700; padding: 2px 9px; border-radius: 99px; display: inline-flex; align-items: center; gap: 4px; }
    .alert-pill.red    { background: #fee2e2; color: #991b1b; }
    .alert-pill.orange { background: #ffedd5; color: #9a3412; }
    .alert-pill.yellow { background: #fef9c3; color: #854d0e; }
    .alert-pill.green  { background: #d1fae5; color: #065f46; }

    .alert-bar-wrap { height: 6px; background: #f0f0f0; border-radius: 99px; overflow: hidden; }
    .alert-bar { height: 100%; border-radius: 99px; transition: width .4s; }
    .alert-bar.bar-success { background: #10b981; }
    .alert-bar.bar-warning { background: #f59e0b; }
    .alert-bar.bar-orange  { background: #f97316; }
    .alert-bar.bar-danger  { background: #ef4444; }

    .alert-row { display: flex; align-items: center; gap: .75rem; padding: .45rem .85rem; border-bottom: 1px solid #f5f5f5; cursor: pointer; transition: background .1s; text-decoration: none; color: inherit; }
    .alert-row:last-child { border-bottom: none; }
    .alert-row:hover { background: #fafafa; }
    .alert-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .alert-dot.dot-success { background: #10b981; }
    .alert-dot.dot-warning { background: #f59e0b; }
    .alert-dot.dot-orange  { background: #f97316; }
    .alert-dot.dot-danger  { background: #ef4444; }
    .alert-type-badge { font-size: .65rem; font-weight: 700; padding: 1px 7px; border-radius: 99px; white-space: nowrap; flex-shrink: 0; }
    .badge-cost { background: #dbeafe; color: #1d4ed8; }
    .badge-km   { background: #f0fdf4; color: #15803d; }
    .badge-tire { background: #fdf4ff; color: #7e22ce; }
</style>
@endpush

@php
    $d_colors = ['sparepart'=>'#3b82f6','labor'=>'#10b981','vendor'=>'#f59e0b','consumable'=>'#8b5cf6'];
    $d_labels = ['sparepart'=>'Sparepart','labor'=>'Labor','vendor'=>'Vendor','consumable'=>'Consumable'];
@endphp

@section('content')

{{-- ══════════ BUDGET & LIMIT ALERTS ══════════ --}}
@php
    $alertItems   = $budgetAlerts['items'];
    $alertRed     = $budgetAlerts['red'];
    $alertOrange  = $budgetAlerts['orange'];
    $alertYellow  = $budgetAlerts['yellow'];
    $alertGreen   = $budgetAlerts['green'];
    $alertTotal   = $alertItems->count();
    // only show if there are yellow/orange/red items
    $alertVisible = ($alertRed + $alertOrange + $alertYellow) > 0;
@endphp
@if($alertTotal > 0)
<div class="alert-widget mb-4" id="alertWidget">
    <div class="alert-widget-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bell-fill text-warning" style="font-size:1rem;"></i>
            <span style="font-size:.85rem;font-weight:700;color:#1a1a2e;">Peringatan Budget & Limit</span>
            <span style="font-size:.75rem;color:#aaa;">{{ now()->format('M Y') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($alertRed > 0)
            <span class="alert-pill red"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $alertRed }} Melewati Batas</span>
            @endif
            @if($alertOrange > 0)
            <span class="alert-pill orange"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $alertOrange }} Kritis (≥80%)</span>
            @endif
            @if($alertYellow > 0)
            <span class="alert-pill yellow"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $alertYellow }} Waspada (≥50%)</span>
            @endif
            @if($alertGreen > 0)
            <span class="alert-pill green"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $alertGreen }} Aman</span>
            @endif
            <button class="btn btn-xs btn-light" style="font-size:.72rem;border-radius:8px;padding:2px 10px;"
                onclick="toggleAlertList()" id="alertToggleBtn">
                <i class="bi bi-chevron-up" id="alertChevron"></i>
            </button>
        </div>
    </div>

    <div id="alertList">
        {{-- Filter tabs --}}
        <div class="px-3 pt-2 pb-1 d-flex align-items-center gap-2 border-bottom" style="background:#fafafa;">
            <span style="font-size:.72rem;color:#aaa;font-weight:600;">Filter:</span>
            <button class="btn btn-xs alert-filter-btn active" data-filter="all"
                style="font-size:.72rem;border-radius:8px;padding:2px 10px;background:#1a1a2e;color:#fff;border:none;">
                Semua ({{ $alertTotal }})
            </button>
            @if($alertRed > 0)
            <button class="btn btn-xs alert-filter-btn" data-filter="danger"
                style="font-size:.72rem;border-radius:8px;padding:2px 10px;background:#fee2e2;color:#991b1b;border:none;">
                Merah ({{ $alertRed }})
            </button>
            @endif
            @if($alertOrange > 0)
            <button class="btn btn-xs alert-filter-btn" data-filter="orange"
                style="font-size:.72rem;border-radius:8px;padding:2px 10px;background:#ffedd5;color:#9a3412;border:none;">
                Orange ({{ $alertOrange }})
            </button>
            @endif
            @if($alertYellow > 0)
            <button class="btn btn-xs alert-filter-btn" data-filter="warning"
                style="font-size:.72rem;border-radius:8px;padding:2px 10px;background:#fef9c3;color:#854d0e;border:none;">
                Kuning ({{ $alertYellow }})
            </button>
            @endif
            @if($alertGreen > 0)
            <button class="btn btn-xs alert-filter-btn" data-filter="success"
                style="font-size:.72rem;border-radius:8px;padding:2px 10px;background:#d1fae5;color:#065f46;border:none;">
                Hijau ({{ $alertGreen }})
            </button>
            @endif
        </div>

        <div id="alertRows" style="max-height: 280px; overflow-y: auto;">
            @foreach($alertItems as $item)
            @php
                $barClass = match($item['color']) {
                    'danger'  => 'bar-danger',
                    'orange'  => 'bar-orange',
                    'warning' => 'bar-warning',
                    default   => 'bar-success',
                };
                $dotClass = match($item['color']) {
                    'danger'  => 'dot-danger',
                    'orange'  => 'dot-orange',
                    'warning' => 'dot-warning',
                    default   => 'dot-success',
                };
                $badgeClass = match($item['type']) {
                    'cost'  => 'badge-cost',
                    'km'    => 'badge-km',
                    default => 'badge-tire',
                };
                $valStr = $item['type'] === 'cost'
                    ? 'IDR ' . number_format($item['used'], 0, ',', '.') . ' / IDR ' . number_format($item['limit'], 0, ',', '.')
                    : number_format($item['used'], 0, ',', '.') . ' / ' . number_format($item['limit'], 0, ',', '.') . ' km';
            @endphp
            <a href="{{ $item['link'] }}" class="alert-row" data-color="{{ $item['color'] }}">
                <div class="alert-dot {{ $dotClass }}"></div>
                <div class="alert-type-badge {{ $badgeClass }}">{{ $item['label'] }}</div>
                <div style="min-width:60px;">
                    <div style="font-size:.78rem;font-weight:700;line-height:1.2;">{{ $item['unit_code'] }}</div>
                    <div style="font-size:.68rem;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">{{ $item['unit_model'] }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="alert-bar-wrap">
                        <div class="alert-bar {{ $barClass }}" style="width:{{ $item['pct'] }}%;"></div>
                    </div>
                </div>
                <div style="font-size:.72rem;color:#666;white-space:nowrap;min-width:90px;text-align:right;">{{ $valStr }}</div>
                <div style="font-size:.75rem;font-weight:800;min-width:40px;text-align:right;color:{{ $item['color'] === 'danger' ? '#dc2626' : ($item['color'] === 'orange' ? '#ea580c' : ($item['color'] === 'warning' ? '#ca8a04' : '#059669')) }};">
                    {{ $item['pct'] }}%
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══════════ PERIOD TABS ══════════ --}}
<div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
    <div class="period-nav" id="periodNav">
        <a class="nav-link active" data-tab="daily"   href="#">Daily</a>
        <a class="nav-link"        data-tab="weekly"  href="#">Weekly</a>
        <a class="nav-link"        data-tab="monthly" href="#">Monthly</a>
    </div>
    <span class="text-muted" style="font-size:.78rem;">
        <i class="bi bi-clock mr-1"></i>{{ now()->format('d M Y, H:i') }} WIB
    </span>
</div>


{{-- ╔══════════════════════════════════════╗
     ║           TAB  DAILY                ║
     ╚══════════════════════════════════════╝ --}}
<div id="tab-daily">

    {{-- KPI Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-tools"></i></div>
                        <span class="kpi-badge warn">{{ $daily['wo_open'] }} Active</span>
                    </div>
                    <div class="kpi-value">{{ $daily['wo_open'] + $daily['wo_completed'] }}</div>
                    <div class="kpi-label">Work Orders Today</div>
                    <div class="d-flex gap-2 mt-2" style="font-size:.75rem;">
                        <span class="text-warning"><i class="bi bi-clock"></i> {{ $daily['wo_open'] }} Open</span>
                        <span class="text-success"><i class="bi bi-check2"></i> {{ $daily['wo_completed'] }} Done</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-clipboard-check"></i></div>
                        <span class="kpi-badge {{ $daily['p2h_fail'] > 0 ? 'down' : 'up' }}">
                            {{ $daily['p2h_fail'] > 0 ? $daily['p2h_fail'].' Fail' : 'All Pass' }}
                        </span>
                    </div>
                    <div class="kpi-value">{{ $daily['p2h_total'] }}</div>
                    <div class="kpi-label">P2H Checks Today</div>
                    <div class="d-flex gap-2 mt-2" style="font-size:.75rem;">
                        <span class="text-success"><i class="bi bi-check-circle"></i> {{ $daily['p2h_pass'] }} Pass</span>
                        <span class="text-danger"><i class="bi bi-x-circle"></i> {{ $daily['p2h_fail'] }} Fail</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#d1fae5;color:#059669;"><i class="bi bi-box-arrow-up"></i></div>
                        <span class="kpi-badge warn">Today</span>
                    </div>
                    <div class="kpi-value">{{ $daily['goods_issue'] }}</div>
                    <div class="kpi-label">Goods Issue Today</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">Items issued &amp; posted</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-speedometer2"></i></div>
                        <span class="kpi-badge {{ $daily['downtime_hours'] > 0 ? 'down' : 'up' }}">
                            {{ $daily['downtime_hours'] > 0 ? 'Alert' : 'Normal' }}
                        </span>
                    </div>
                    <div class="kpi-value">{{ $daily['downtime_hours'] }}<small style="font-size:1rem;">h</small></div>
                    <div class="kpi-label">Downtime Today</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">Total unit downtime hours</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hourly WO Chart + P2H Donut --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title">Work Order Activity – Today</div>
                        <div class="section-sub">Hourly transaction volume</div>
                    </div>
                </div>
                <div class="card-body" style="position:relative;height:260px;">
                    <canvas id="d_woHourly"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">P2H Result Today</div>
                    <div class="section-sub">Pass vs. Fail ratio</div>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="width:170px;height:170px;position:relative;">
                        <canvas id="d_p2hDonut"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                            <div style="font-size:1.5rem;font-weight:800;line-height:1;">{{ $daily['p2h_total'] }}</div>
                            <div style="font-size:.65rem;color:#aaa;">TOTAL</div>
                        </div>
                    </div>
                    <div class="mini-legend mt-3">
                        <span><span class="legend-dot dot-green"></span><small class="text-muted">Pass ({{ $daily['p2h_pass'] }})</small></span>
                        <span><span class="legend-dot dot-red"></span><small class="text-muted">Fail ({{ $daily['p2h_fail'] }})</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cost Breakdown + Recent WO --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">Cost Breakdown – Today</div>
                    <div class="section-sub">IDR {{ number_format($daily['repair_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="card-body">
                    @php $d_max = max(array_values($daily['cost_breakdown'])) ?: 1; @endphp
                    @foreach($daily['cost_breakdown'] as $key => $val)
                    <div class="breakdown-item">
                        <div class="breakdown-label">
                            <span><span class="legend-dot" style="background:{{ $d_colors[$key] }};"></span>{{ $d_labels[$key] }}</span>
                            <strong>{{ number_format($val, 0, ',', '.') }}</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width:{{ ($val/$d_max)*100 }}%;background:{{ $d_colors[$key] }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="section-title">Recent Work Orders – Today</div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.75rem;">
                        View All <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>WO #</th><th>Unit</th><th>Type</th><th>Technician</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($daily['recent_wo'] as $wo)
                                <tr>
                                    <td><a href="{{ route('work-orders.show', $wo) }}" style="font-weight:700;font-size:.82rem;">{{ $wo->wo_number }}</a></td>
                                    <td style="font-size:.82rem;">{{ $wo->unit->unit_code ?? '-' }}</td>
                                    <td><span class="badge badge-soft-{{ $wo->maintenance_type==='corrective'?'danger':($wo->maintenance_type==='preventive'?'success':'info') }}" style="border-radius:20px;font-size:.68rem;">{{ ucfirst($wo->maintenance_type) }}</span></td>
                                    <td style="font-size:.82rem;">{{ $wo->technician->technician_name ?? '-' }}</td>
                                    <td>@include('components.status-badge', ['status'=>$wo->status])</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.82rem;"><i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>No work orders today</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /tab-daily --}}


{{-- ╔══════════════════════════════════════╗
     ║           TAB  WEEKLY               ║
     ╚══════════════════════════════════════╝ --}}
<div id="tab-weekly" style="display:none;">

    {{-- KPI Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-tools"></i></div>
                        <span class="kpi-badge warn">This Week</span>
                    </div>
                    <div class="kpi-value">{{ $weekly['wo_total'] }}</div>
                    <div class="kpi-label">Work Orders</div>
                    <div class="d-flex gap-2 mt-2" style="font-size:.75rem;">
                        <span class="text-success"><i class="bi bi-check2"></i> {{ $weekly['wo_completed'] }} Done</span>
                        <span class="text-warning"><i class="bi bi-clock"></i> {{ $weekly['wo_total'] - $weekly['wo_completed'] }} Open</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-truck"></i></div>
                        <span class="kpi-badge up">Fleet</span>
                    </div>
                    <div class="kpi-value">{{ $totalUnits }}</div>
                    <div class="kpi-label">Total Units</div>
                    <div class="d-flex gap-2 mt-2" style="font-size:.75rem;">
                        <span class="text-success"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $available }}</span>
                        <span class="text-danger"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $underRepair }}</span>
                        <span class="text-warning"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $standby }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#d1fae5;color:#059669;"><i class="bi bi-box-arrow-up"></i></div>
                        <span class="kpi-badge warn">Week</span>
                    </div>
                    <div class="kpi-value">{{ $weekly['goods_issue'] }}</div>
                    <div class="kpi-label">Goods Issue</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">Posted this week</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-cash-stack"></i></div>
                        <span class="kpi-badge warn">Week</span>
                    </div>
                    <div class="kpi-value" style="font-size:1.3rem;">{{ number_format($weekly['repair_cost']/1000000, 1) }}M</div>
                    <div class="kpi-label">Repair Cost</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">IDR {{ number_format($weekly['repair_cost'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- WO per Day + Availability Line --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title">Work Order per Day – This Week</div>
                        <div class="section-sub">Open vs Completed per hari</div>
                    </div>
                    <span class="chip">Weekly</span>
                </div>
                <div class="card-body" style="position:relative;height:260px;">
                    <canvas id="w_woDaily"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">Availability Trend</div>
                    <div class="section-sub">Unit readiness this week</div>
                </div>
                <div class="card-body" style="position:relative;height:260px;">
                    <canvas id="w_availLine"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Unit Status + Cost Breakdown + Top Sparepart --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">Unit Status</div>
                    <div class="section-sub">Available / Repair / Standby</div>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="width:170px;height:170px;position:relative;">
                        <canvas id="w_unitDonut"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                            <div style="font-size:1.5rem;font-weight:800;line-height:1;">{{ $totalUnits }}</div>
                            <div style="font-size:.65rem;color:#aaa;">UNITS</div>
                        </div>
                    </div>
                    <div class="mini-legend mt-3">
                        <span><span class="legend-dot dot-green"></span><small>{{ $available }} Avail</small></span>
                        <span><span class="legend-dot dot-red"></span><small>{{ $underRepair }} Repair</small></span>
                        <span><span class="legend-dot dot-yellow"></span><small>{{ $standby }} Standby</small></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">Cost Breakdown – This Week</div>
                    <div class="section-sub">IDR {{ number_format($weekly['repair_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="card-body">
                    @php $w_max = max(array_values($weekly['cost_breakdown'])) ?: 1; @endphp
                    @foreach($weekly['cost_breakdown'] as $key => $val)
                    <div class="breakdown-item">
                        <div class="breakdown-label">
                            <span><span class="legend-dot" style="background:{{ $d_colors[$key] }};"></span>{{ $d_labels[$key] }}</span>
                            <strong>{{ number_format($val, 0, ',', '.') }}</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width:{{ ($val/$w_max)*100 }}%;background:{{ $d_colors[$key] }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title">Top Sparepart – Week</div>
                    <div class="section-sub">Highest usage this week</div>
                </div>
                <div class="card-body p-0">
                    @forelse($weekly['top_spareparts'] as $sp)
                    <div class="activity-row px-3">
                        <div class="activity-dot" style="background:#3b82f6;"></div>
                        <div class="flex-grow-1">
                            <div style="font-size:.82rem;font-weight:600;">{{ Str::limit($sp->part_name, 30) }}</div>
                            <div style="font-size:.72rem;color:#aaa;">IDR {{ number_format($sp->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:.82rem;">No data this week</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent WO Weekly --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="section-title">Recent Work Orders – This Week</div>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.75rem;">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>WO #</th><th>Unit</th><th>Type</th><th>Technician</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($weekly['recent_wo'] as $wo)
                        <tr>
                            <td><a href="{{ route('work-orders.show', $wo) }}" style="font-weight:700;font-size:.82rem;">{{ $wo->wo_number }}</a></td>
                            <td style="font-size:.82rem;">{{ $wo->unit->unit_code ?? '-' }} <span class="text-muted">{{ $wo->unit->unit_model ?? '' }}</span></td>
                            <td><span class="badge badge-soft-{{ $wo->maintenance_type==='corrective'?'danger':($wo->maintenance_type==='preventive'?'success':'info') }}" style="border-radius:20px;font-size:.68rem;">{{ ucfirst($wo->maintenance_type) }}</span></td>
                            <td style="font-size:.82rem;">{{ $wo->technician->technician_name ?? '-' }}</td>
                            <td>@include('components.status-badge', ['status'=>$wo->status])</td>
                            <td style="font-size:.78rem;color:#aaa;">{{ $wo->created_at->format('d M') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem;"><i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>No work orders this week</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- /tab-weekly --}}


{{-- ╔══════════════════════════════════════╗
     ║           TAB  MONTHLY              ║
     ╚══════════════════════════════════════╝ --}}
<div id="tab-monthly" style="display:none;">

    {{-- KPI Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-truck"></i></div>
                        <span class="kpi-badge up">{{ $available }} Ready</span>
                    </div>
                    <div class="kpi-value">{{ $totalUnits }}</div>
                    <div class="kpi-label">Total Unit Fleet</div>
                    <div class="d-flex gap-3 mt-2" style="font-size:.75rem;">
                        <span class="text-success"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $available }} Ready</span>
                        <span class="text-danger"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $underRepair }} Repair</span>
                        <span class="text-warning"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> {{ $standby }} Standby</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-tools"></i></div>
                        <span class="kpi-badge warn">{{ $openWO }} Open</span>
                    </div>
                    <div class="kpi-value">{{ $monthly['wo_total'] }}</div>
                    <div class="kpi-label">Work Orders – Month</div>
                    <div class="d-flex gap-2 mt-2" style="font-size:.75rem;">
                        <span class="text-warning"><i class="bi bi-clock"></i> {{ $monthly['wo_total'] - $monthly['wo_completed'] }} Active</span>
                        <span class="text-success"><i class="bi bi-check2-all"></i> {{ $monthly['wo_completed'] }} Done</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-cart-check"></i></div>
                        @if($openPR + $openPO > 0)
                        <span class="kpi-badge warn">{{ $openPR + $openPO }} Pending</span>
                        @endif
                    </div>
                    <div class="kpi-value" style="font-size:1.5rem;">{{ $openPR }} PR / {{ $openPO }} PO</div>
                    <div class="kpi-label">Procurement</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">Open procurement docs</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi-card mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#d1fae5;color:#059669;"><i class="bi bi-box-seam"></i></div>
                        @if($lowStockCount > 0)
                        <span class="kpi-badge down">{{ $lowStockCount }} Low</span>
                        @else
                        <span class="kpi-badge up">OK</span>
                        @endif
                    </div>
                    <div class="kpi-value">{{ $totalSpareparts }}</div>
                    <div class="kpi-label">Warehouse Stock</div>
                    <div class="mt-2" style="font-size:.75rem;color:#aaa;">{{ $lowStockCount }} items below minimum</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Cost Card + Cost Breakdown Donut + Unit Status Donut --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card h-100 summary-highlight">
                <div class="card-body p-3">
                    <div class="kpi-label mb-1"><i class="bi bi-cash-stack mr-1"></i> Total Repair Cost (All Time)</div>
                    <div style="font-size:1.7rem;font-weight:800;color:#dc2626;">IDR {{ number_format($totalRepairCostAll, 0, ',', '.') }}</div>
                    <hr style="border-color:#f0f0f0;">
                    @foreach($monthly['cost_breakdown_all'] as $key => $val)
                    <div class="summary-row">
                        <span class="s-label"><span class="legend-dot" style="background:{{ $d_colors[$key] }};"></span> {{ $d_labels[$key] }}</span>
                        <span class="s-val">{{ number_format($val, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-pie-chart mr-2"></i> Cost Breakdown – Month</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width:210px;height:210px;">
                        <canvas id="m_costDonut"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-speedometer2 mr-2"></i> Unit Status</div>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="width:180px;height:180px;position:relative;">
                        <canvas id="m_unitDonut"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                            <div style="font-size:1.6rem;font-weight:800;line-height:1;">{{ $totalUnits }}</div>
                            <div style="font-size:.65rem;color:#aaa;">UNITS</div>
                        </div>
                    </div>
                    <div class="mini-legend mt-3">
                        <span><span class="legend-dot dot-green"></span><small>Available ({{ $available }})</small></span>
                        <span><span class="legend-dot dot-red"></span><small>Repair ({{ $underRepair }})</small></span>
                        <span><span class="legend-dot dot-yellow"></span><small>Standby ({{ $standby }})</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Availability Trend + Monthly Cost Trend --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title"><i class="bi bi-graph-up mr-2"></i>Availability Trend</div>
                        <div class="section-sub">Daily average availability %</div>
                    </div>
                    <span class="chip"><i class="bi bi-calendar3 mr-1"></i>All Period</span>
                </div>
                <div class="card-body" style="position:relative;height:260px;">
                    <canvas id="m_availTrend"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-bar-chart mr-2"></i>Monthly Cost Trend</div>
                    <div class="section-sub">Repair cost per month (12 bulan)</div>
                </div>
                <div class="card-body" style="position:relative;height:260px;">
                    <canvas id="m_monthlyCost"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Repair Cost per Unit + Top Sparepart --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-truck mr-2"></i>Repair Cost per Unit</div>
                    <div class="section-sub">Top 10 highest cost units</div>
                </div>
                <div class="card-body" style="position:relative;height:280px;">
                    <canvas id="m_costPerUnit"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-gear mr-2"></i>Top Sparepart Usage Cost</div>
                    <div class="section-sub">Most expensive parts consumed this month</div>
                </div>
                <div class="card-body" style="position:relative;height:280px;">
                    <canvas id="m_sparepartCost"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Complaint Types --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title"><i class="bi bi-exclamation-triangle mr-2"></i>Top Complaint Types This Month</div>
                        <div class="section-sub">Most common damage types</div>
                    </div>
                    <a href="{{ route('reports.complaint-analysis') }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px;font-size:.75rem;">View Detailed Report</a>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        @forelse($monthly['top_complaints'] as $cx)
                        <?php $ct = $cx->complaintType; ?>
                        @if($ct)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-start-4" style="border-left-color:{{ $ct->color ?? '#6c757d' }} !important; border-top:none; border-right:none; border-bottom:none;">
                                <div class="card-body p-3">
                                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:.5rem;">
                                        <div>
                                            <div style="font-size:.72rem;color:#888;text-transform:uppercase;letter-spacing:.4px;font-weight:600;">{{ $ct->name }}</div>
                                        </div>
                                        <span class="badge" style="background-color:{{ $ct->color }}dd; color:{{ $ct->color }}; border-radius:4px; padding:.3rem .6rem; font-size:.7rem; font-weight:700;">{{ $cx->total }} WO</span>
                                    </div>
                                    <div style="font-size:1.6rem;font-weight:800;line-height:1;margin-bottom:.4rem;">{{ $cx->total }}</div>
                                    <div style="font-size:.75rem;color:#666;">
                                        <div>📊 Downtime: {{ number_format($cx->total_downtime, 1) }} hrs</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-info-circle d-block mb-2" style="font-size:2.5rem;"></i>
                                <small>No complaint data yet</small>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- WO by Type + Units Attention + Low Stock --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-title"><i class="bi bi-wrench mr-2"></i>WO by Type</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width:180px;height:180px;"><canvas id="m_woType"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="section-title"><i class="bi bi-exclamation-triangle text-warning mr-2"></i>Units Needing Attention</div>
                    <span class="badge badge-soft-danger" style="border-radius:20px;">{{ $unitsAttention->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height:280px;overflow-y:auto;">
                    @forelse($unitsAttention as $ua)
                    <div class="activity-row px-3">
                        <div class="activity-dot" style="background:#ef4444;"></div>
                        <div class="flex-grow-1">
                            <a href="{{ route('units.show', $ua) }}" style="font-weight:700;font-size:.82rem;">{{ $ua->unit_code }}</a>
                            <div style="font-size:.72rem;color:#aaa;">{{ $ua->unit_model }} &middot; {{ $ua->category->name ?? '' }}</div>
                        </div>
                        @include('components.status-badge', ['status'=>$ua->current_status])
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success d-block mb-1" style="font-size:2rem;"></i>
                        <small>All units operational</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="section-title"><i class="bi bi-box-seam text-danger mr-2"></i>Low Stock Alert</div>
                    <span class="badge badge-soft-warning" style="border-radius:20px;">{{ $lowStockCount }} items</span>
                </div>
                <div class="card-body p-0" style="max-height:280px;overflow-y:auto;">
                    @forelse($lowStockParts as $lsp)
                    <div class="activity-row px-3 justify-content-between">
                        <div>
                            <div style="font-weight:600;font-size:.82rem;">{{ $lsp->part_number }}</div>
                            <div style="font-size:.72rem;color:#aaa;">{{ $lsp->part_name }}</div>
                        </div>
                        <div class="text-right">
                            <div style="font-weight:700;color:#dc2626;font-size:.85rem;">{{ $lsp->stock_on_hand }} {{ $lsp->uom }}</div>
                            <div style="font-size:.7rem;color:#aaa;">Min: {{ $lsp->minimum_stock }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success d-block mb-1" style="font-size:2rem;"></i>
                        <small>All stock levels OK</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Work Orders Monthly --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="section-title"><i class="bi bi-clock-history mr-2"></i>Recent Work Orders</div>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.75rem;">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>WO Number</th><th>Unit</th><th>Type</th><th>Technician</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($monthly['recent_wo'] as $wo)
                        <tr>
                            <td><a href="{{ route('work-orders.show', $wo) }}" style="font-weight:700;">{{ $wo->wo_number }}</a></td>
                            <td>{{ $wo->unit->unit_code ?? '-' }} <span class="text-muted" style="font-size:.75rem;">{{ $wo->unit->unit_model ?? '' }}</span></td>
                            <td><span class="badge badge-soft-{{ $wo->maintenance_type==='corrective'?'danger':($wo->maintenance_type==='preventive'?'success':'info') }}" style="border-radius:20px;">{{ ucfirst($wo->maintenance_type) }}</span></td>
                            <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                            <td>@include('components.status-badge', ['status'=>$wo->status])</td>
                            <td style="font-size:.78rem;color:#aaa;">{{ $wo->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>No work orders this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- /tab-monthly --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab Switcher ──────────────────────────
    const tabMap = { daily: 'tab-daily', weekly: 'tab-weekly', monthly: 'tab-monthly' };
    document.querySelectorAll('#periodNav .nav-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#periodNav .nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            Object.values(tabMap).forEach(id => document.getElementById(id).style.display = 'none');
            document.getElementById(tabMap[this.dataset.tab]).style.display = '';
        });
    });

    // ── Helpers ───────────────────────────────
    const fmt  = v => 'IDR ' + new Intl.NumberFormat('id-ID').format(v);
    const fmtM = v => (v / 1000000).toFixed(1) + 'M';
    const C = { red:'#dc2626', blue:'#3b82f6', green:'#10b981', yellow:'#f59e0b', purple:'#8b5cf6', gray:'#94a3b8' };
    const COST_COLORS = ['#3b82f6','#10b981','#f59e0b','#8b5cf6'];

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#999';

    // ═══════════════════════════════
    // DAILY
    // ═══════════════════════════════
    new Chart(document.getElementById('d_woHourly'), {
        type: 'bar',
        data: {
            labels: Array.from({length:24}, (_,i) => String(i).padStart(2,'0')+':00'),
            datasets: [{
                label: 'Work Orders',
                data: @json($daily['wo_hourly']),
                backgroundColor: 'rgba(220,38,38,.75)',
                borderRadius: 6, maxBarThickness: 32
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    new Chart(document.getElementById('d_p2hDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Pass','Fail'],
            datasets: [{ data: [{{ $daily['p2h_pass'] }},{{ $daily['p2h_fail'] }}], backgroundColor: [C.green, C.red], borderWidth: 0, spacing: 3 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
    });

    // ═══════════════════════════════
    // WEEKLY
    // ═══════════════════════════════
    const woDailyData = @json($weekly['wo_daily']);
    new Chart(document.getElementById('w_woDaily'), {
        type: 'bar',
        data: {
            labels: woDailyData.labels,
            datasets: [
                { label: 'Open',      data: woDailyData.open,   backgroundColor: C.red,   borderRadius: 5, maxBarThickness: 28 },
                { label: 'Completed', data: woDailyData.closed, backgroundColor: C.green, borderRadius: 5, maxBarThickness: 28 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { boxWidth: 10 } } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    const wAvail = @json($weekly['avail_trend']);
    new Chart(document.getElementById('w_availLine'), {
        type: 'line',
        data: {
            labels: wAvail.labels,
            datasets: [{
                label: 'Availability %', data: wAvail.values,
                borderColor: C.red, backgroundColor: 'rgba(220,38,38,.08)',
                fill: true, tension: .4, borderWidth: 2.5,
                pointRadius: 4, pointBackgroundColor: C.red, pointBorderColor: '#fff', pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y.toFixed(1)+'%' } } },
            scales: {
                x: { grid: { display: false } },
                y: { min: 0, max: 100, ticks: { callback: v => v+'%', stepSize: 25 }, grid: { color: 'rgba(0,0,0,.04)' } }
            }
        }
    });

    new Chart(document.getElementById('w_unitDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Available','Under Repair','Standby'],
            datasets: [{ data: [{{ $available }},{{ $underRepair }},{{ $standby }}], backgroundColor: [C.green, C.red, C.yellow], borderWidth: 0, spacing: 3 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
    });

    // ═══════════════════════════════
    // MONTHLY
    // ═══════════════════════════════
    const mCB = @json($monthly['cost_breakdown']);
    new Chart(document.getElementById('m_costDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Sparepart','Labor','Vendor','Consumable'],
            datasets: [{ data: [mCB.sparepart, mCB.labor, mCB.vendor, mCB.consumable], backgroundColor: COST_COLORS, borderWidth: 0, spacing: 3 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ctx.label+': '+fmt(ctx.parsed) } }
            }
        }
    });

    new Chart(document.getElementById('m_unitDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Available','Under Repair','Standby'],
            datasets: [{ data: [{{ $available }},{{ $underRepair }},{{ $standby }}], backgroundColor: [C.green, C.red, C.yellow], borderWidth: 0, spacing: 3 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '72%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label+': '+ctx.parsed+' units' } } }
        }
    });

    const mAvail = @json($monthly['avail_trend']);
    if (mAvail.labels && mAvail.labels.length > 0) {
        new Chart(document.getElementById('m_availTrend'), {
            type: 'line',
            data: {
                labels: mAvail.labels,
                datasets: [{
                    label: 'Avg Availability %', data: mAvail.values,
                    borderColor: C.red, backgroundColor: 'rgba(220,38,38,.06)',
                    fill: true, tension: .4, borderWidth: 2.5,
                    pointRadius: 3, pointBackgroundColor: C.red, pointBorderColor: '#fff', pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y.toFixed(1)+'%' } } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 15 } },
                    y: { min: 0, max: 100, ticks: { callback: v => v+'%', stepSize: 25 }, grid: { color: 'rgba(0,0,0,.04)' } }
                }
            }
        });
    }

    const mct = @json($monthly['monthly_cost_trend']);
    if (mct.length > 0) {
        new Chart(document.getElementById('m_monthlyCost'), {
            type: 'bar',
            data: {
                labels: mct.map(d => d.month),
                datasets: [
                    { label: 'Sparepart', data: mct.map(d => parseFloat(d.sparepart)), backgroundColor: C.blue,   borderRadius: 5, barPercentage: .75 },
                    { label: 'Labor',     data: mct.map(d => parseFloat(d.labor)),     backgroundColor: C.green,  borderRadius: 5, barPercentage: .75 },
                    { label: 'Vendor',    data: mct.map(d => parseFloat(d.vendor)),    backgroundColor: C.yellow, borderRadius: 5, barPercentage: .75 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label+': '+fmt(ctx.parsed.y) } }
                },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, ticks: { callback: fmtM }, grid: { color: 'rgba(0,0,0,.04)' } }
                }
            }
        });
    }

    const costPerUnit = @json($monthly['cost_per_unit']);
    if (costPerUnit.length > 0) {
        new Chart(document.getElementById('m_costPerUnit'), {
            type: 'bar',
            data: {
                labels: costPerUnit.map(d => d.unit ? d.unit.unit_code : 'N/A'),
                datasets: [{
                    label: 'Total Cost',
                    data: costPerUnit.map(d => parseFloat(d.total)),
                    backgroundColor: ['#dc2626','#ef4444','#f87171','#fca5a5','#fecaca','#fee2e2','#fef2f2','#fff5f5','#fff8f8','#fffafa'],
                    borderRadius: 8, barPercentage: .7
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => fmt(ctx.parsed.x) } } },
                scales: {
                    x: { ticks: { callback: fmtM }, grid: { color: 'rgba(0,0,0,.04)' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    const spData = @json($monthly['top_spareparts']);
    if (spData.length > 0) {
        new Chart(document.getElementById('m_sparepartCost'), {
            type: 'bar',
            data: {
                labels: spData.map(d => d.part_name.length > 28 ? d.part_name.substring(0,28)+'...' : d.part_name),
                datasets: [{
                    label: 'Cost',
                    data: spData.map(d => parseFloat(d.total)),
                    backgroundColor: ['#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#dbeafe','#eff6ff','#f5f9ff','#f8fbff'],
                    borderRadius: 8, barPercentage: .7
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => fmt(ctx.parsed.x) } } },
                scales: {
                    x: { ticks: { callback: fmtM }, grid: { color: 'rgba(0,0,0,.04)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    const woType = @json($woByType);
    if (woType.length > 0) {
        const typeColors = { corrective: C.red, preventive: C.green, predictive: C.blue };
        new Chart(document.getElementById('m_woType'), {
            type: 'doughnut',
            data: {
                labels: woType.map(d => d.maintenance_type.charAt(0).toUpperCase() + d.maintenance_type.slice(1)),
                datasets: [{
                    data: woType.map(d => d.total),
                    backgroundColor: woType.map(d => typeColors[d.maintenance_type] || C.gray),
                    borderWidth: 0, spacing: 3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } } }
            }
        });
    }

});

// ── Budget Alert Widget ──────────────────────────
function toggleAlertList() {
    const list    = document.getElementById('alertList');
    const chevron = document.getElementById('alertChevron');
    if (!list) return;
    const hidden = list.style.display === 'none';
    list.style.display = hidden ? '' : 'none';
    chevron.className = hidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
}

document.querySelectorAll('.alert-filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.alert-filter-btn').forEach(b => {
            b.style.background = '';
            b.style.color = '';
            b.classList.remove('active');
        });
        this.classList.add('active');
        this.style.background = '#1a1a2e';
        this.style.color = '#fff';

        const filter = this.dataset.filter;
        document.querySelectorAll('#alertRows .alert-row').forEach(function(row) {
            row.style.display = (filter === 'all' || row.dataset.color === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
