@extends('layouts.app')
@section('page-title', 'Laporan & Analisa Operasi')
@section('breadcrumb')
<li class="breadcrumb-item active">Laporan Operasi</li>
@endsection

@section('content')
{{-- Filter Periode --}}
<x-card class="mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-danger w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
        </div>
        <div class="col-md-4 text-muted small d-flex align-items-end">
            Periode: <strong class="ms-1">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong>
            &nbsp;–&nbsp;
            <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
        </div>
    </form>
</x-card>

{{-- ── RINGKASAN CARDS ── --}}
<div class="row g-3 mb-3">
    {{-- P2H --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #16a34a!important;">
            <div class="card-body">
                <div class="text-muted small mb-1"><i class="bi bi-clipboard-check me-1"></i>Total P2H</div>
                <div class="fw-bold fs-2">{{ $totalP2H }}</div>
                <div class="d-flex gap-2 mt-2 flex-wrap" style="font-size:.78rem;">
                    <span class="badge bg-success">{{ $totalLayak }} Layak</span>
                    <span class="badge bg-warning text-dark">{{ $totalCatatan }} Catatan</span>
                    <span class="badge bg-danger">{{ $totalTidakLayak }} Tidak Layak</span>
                </div>
                @if($totalP2H > 0)
                <div class="mt-2">
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-success" style="width:{{ round($totalLayak/$totalP2H*100) }}%"></div>
                        <div class="progress-bar bg-warning" style="width:{{ round($totalCatatan/$totalP2H*100) }}%"></div>
                        <div class="progress-bar bg-danger" style="width:{{ round($totalTidakLayak/$totalP2H*100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ round($totalLayak/$totalP2H*100) }}% layak</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Timesheet / Retase --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #7c3aed!important;">
            <div class="card-body">
                <div class="text-muted small mb-1"><i class="bi bi-clock-history me-1"></i>Produktivitas Shift</div>
                <div class="fw-bold fs-2 text-purple" style="color:#7c3aed;">{{ number_format($totalRetase) }}</div>
                <div class="text-muted small">Total Retase (Trip)</div>
                <div class="mt-2 d-flex gap-3" style="font-size:.83rem;">
                    <div>
                        <div class="fw-semibold">{{ $totalTS }}</div>
                        <div class="text-muted" style="font-size:.75rem;">Shift Tercatat</div>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $avgRetase }}</div>
                        <div class="text-muted" style="font-size:.75rem;">Rata-rata/Shift</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HM Usage --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #0891b2!important;">
            <div class="card-body">
                <div class="text-muted small mb-1"><i class="bi bi-speedometer2 me-1"></i>Total Jam Operasi</div>
                <div class="fw-bold fs-2 text-info" style="color:#0891b2;">{{ number_format($totalHmUsage, 1) }}</div>
                <div class="text-muted small">Jam (dari HM)</div>
                <div class="mt-2" style="font-size:.83rem;">
                    <div class="fw-semibold">{{ $totalTS > 0 ? number_format($totalHmUsage / $totalTS, 1) : 0 }} jam/shift</div>
                    <div class="text-muted" style="font-size:.75rem;">Rata-rata jam kerja</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FTW --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1d4ed8!important;">
            <div class="card-body">
                <div class="text-muted small mb-1"><i class="bi bi-heart-pulse me-1"></i>Fit to Work</div>
                <div class="fw-bold fs-2" style="color:#1d4ed8;">{{ $totalFit }}<span class="fs-6 text-muted">/{{ $totalFTW }}</span></div>
                <div class="text-muted small">Operator Fit</div>
                @if($totalFTW > 0)
                <div class="mt-2">
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar" style="background:#1d4ed8;width:{{ round($totalFit/$totalFTW*100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ round($totalFit/$totalFTW*100) }}% fit rate</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── PER UNIT ── --}}
<x-card class="mb-3">
    <x-slot:header>
        <div class="section-title"><i class="bi bi-truck me-2"></i>Produktivitas per Unit</div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Unit</th>
                    <th>Kategori</th>
                    <th class="text-center">Total P2H</th>
                    <th class="text-center">Layak</th>
                    <th class="text-center">Tidak Layak</th>
                    <th class="text-center">Shift</th>
                    <th class="text-center">Total Retase</th>
                    <th class="text-center">Avg Retase/Shift</th>
                    <th class="text-center">HM Digunakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unitStats as $i => $unit)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $unit->unit_code }}</div>
                        <small class="text-muted">{{ $unit->unit_model }}</small>
                    </td>
                    <td><small>{{ $unit->category?->name ?? '-' }}</small></td>
                    <td class="text-center">{{ $unit->total_p2h }}</td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $unit->p2h_layak }}</span>
                    </td>
                    <td class="text-center">
                        @if($unit->p2h_tidak_layak > 0)
                        <span class="badge bg-danger">{{ $unit->p2h_tidak_layak }}</span>
                        @else
                        <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $unit->total_shift }}</td>
                    <td class="text-center fw-bold text-primary fs-6">{{ number_format($unit->total_retase) }}</td>
                    <td class="text-center">{{ $unit->avg_retase }}</td>
                    <td class="text-center">{{ number_format($unit->total_hm_usage, 1) }} <small class="text-muted">jam</small></td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- ── PER OPERATOR ── --}}
<x-card class="mb-3">
    <x-slot:header>
        <div class="section-title"><i class="bi bi-person-badge me-2"></i>Produktivitas per Operator</div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Operator</th>
                    <th class="text-center">FTW</th>
                    <th class="text-center">Total P2H</th>
                    <th class="text-center">Shift</th>
                    <th class="text-center">Total Retase</th>
                    <th class="text-center">Avg Retase/Shift</th>
                    <th class="text-center">HM Digunakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($operatorStats as $i => $op)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $op->operator_name }}</div>
                        <small class="text-muted">{{ $op->operator_code }}</small>
                    </td>
                    <td class="text-center">
                        @if($op->ftw_total > 0)
                        <span class="badge bg-{{ $op->ftw_fit === $op->ftw_total ? 'success' : 'warning text-dark' }}">
                            {{ $op->ftw_fit }}/{{ $op->ftw_total }}
                        </span>
                        @else
                        <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $op->total_p2h }}</td>
                    <td class="text-center">{{ $op->total_shift }}</td>
                    <td class="text-center fw-bold text-primary">{{ number_format($op->total_retase) }}</td>
                    <td class="text-center">{{ $op->avg_retase }}</td>
                    <td class="text-center">{{ number_format($op->total_hm, 1) }} <small class="text-muted">jam</small></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- ── TREND HARIAN ── --}}
@if($dailyTrend->count())
<x-card>
    <x-slot:header>
        <div class="section-title"><i class="bi bi-graph-up me-2"></i>Trend Harian – Retase & Jam Operasi</div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" style="font-size:.84rem;">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center">Shift Aktif</th>
                    <th class="text-center">Total Retase</th>
                    <th class="text-center">Total HM (jam)</th>
                    <th style="width:200px;">Retase (bar)</th>
                </tr>
            </thead>
            <tbody>
                @php $maxRetase = $dailyTrend->max('total_retase') ?: 1; @endphp
                @foreach($dailyTrend as $day)
                <tr>
                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($day->shift_date)->format('d M Y') }}</td>
                    <td class="text-center">{{ $day->total_shift }}</td>
                    <td class="text-center fw-bold text-primary">{{ number_format($day->total_retase) }}</td>
                    <td class="text-center">{{ number_format($day->total_hm, 1) }}</td>
                    <td>
                        <div class="progress" style="height:14px;">
                            <div class="progress-bar bg-primary"
                                style="width:{{ round($day->total_retase / $maxRetase * 100) }}%;font-size:.7rem;line-height:14px;">
                                {{ $day->total_retase > 0 ? $day->total_retase : '' }}
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total</td>
                    <td class="text-center">{{ $totalTS }}</td>
                    <td class="text-center text-primary">{{ number_format($totalRetase) }}</td>
                    <td class="text-center">{{ number_format($totalHmUsage, 1) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-card>
@endif
@endsection
