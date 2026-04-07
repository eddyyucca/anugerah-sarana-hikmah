@extends('layouts.app')
@section('page-title', 'Log Operasi Harian')
@section('breadcrumb')
<li class="breadcrumb-item active">Log Operasi</li>
@endsection

@section('content')
<x-alerts />

{{-- Filter --}}
<x-card class="mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label form-label-sm mb-1">Dari</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm mb-1">Sampai</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm mb-1">Unit</label>
            <select name="unit_id" class="form-select form-select-sm">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->unit_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Operator</label>
            <select name="operator_id" class="form-select form-select-sm">
                <option value="">Semua Operator</option>
                @foreach($operators as $op)
                <option value="{{ $op->id }}" {{ request('operator_id') == $op->id ? 'selected' : '' }}>
                    {{ $op->operator_code }} - {{ $op->operator_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm mb-1">Shift</label>
            <select name="shift" class="form-select form-select-sm">
                <option value="">Semua Shift</option>
                <option value="day" {{ request('shift') === 'day' ? 'selected' : '' }}>Pagi</option>
                <option value="night" {{ request('shift') === 'night' ? 'selected' : '' }}>Malam</option>
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-sm btn-danger"><i class="bi bi-search"></i></button>
            <a href="{{ route('operasi.log') }}" class="btn btn-sm btn-light"><i class="bi bi-x"></i></a>
        </div>
    </form>
</x-card>

{{-- Log Table --}}
<x-card>
    <x-slot:header>
        <div class="section-title">
            <i class="bi bi-journal-text me-2"></i>Log Operasi
            <small class="text-muted fw-normal ms-2">{{ $dateFrom }} s.d. {{ $dateTo }}</small>
        </div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Unit</th>
                    <th>Operator</th>
                    {{-- FTW --}}
                    <th class="text-center" style="background:#1d4ed8;">
                        <i class="bi bi-heart-pulse me-1"></i>FTW
                    </th>
                    {{-- P2H --}}
                    <th class="text-center" style="background:#15803d;">
                        <i class="bi bi-clipboard-check me-1"></i>P2H
                    </th>
                    <th class="text-center" style="background:#15803d;">HM Awal</th>
                    {{-- Timesheet --}}
                    <th class="text-center" style="background:#7c3aed;">
                        <i class="bi bi-clock-history me-1"></i>Retase
                    </th>
                    <th class="text-center" style="background:#7c3aed;">Jam Kerja</th>
                    <th class="text-center" style="background:#7c3aed;">HM Akhir</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($p2hList as $p2h)
                @php
                    $ftwKey = $p2h->operator_id . '_' . $p2h->check_date->format('Y-m-d') . '_' . $p2h->shift;
                    $ftw    = $ftwMap[$ftwKey] ?? null;
                    $ts     = $p2h->timesheet;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $p2h->check_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $p2h->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                            {{ $p2h->shift === 'day' ? 'Pagi' : 'Malam' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $p2h->unit->unit_code }}</div>
                        <small class="text-muted">{{ $p2h->unit->unit_model }}</small>
                    </td>
                    <td>{{ $p2h->operator->operator_name }}</td>

                    {{-- FTW Column --}}
                    <td class="text-center">
                        @if($ftw)
                            <span class="badge bg-{{ $ftw->is_fit ? 'success' : 'danger' }}">
                                {{ $ftw->is_fit ? 'Fit' : 'Unfit' }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted border">-</span>
                        @endif
                    </td>

                    {{-- P2H Columns --}}
                    <td class="text-center">
                        <span class="badge
                            {{ $p2h->overall_status === 'layak' ? 'bg-success' : ($p2h->overall_status === 'layak_catatan' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ $p2h->overall_status === 'layak' ? 'Layak' : ($p2h->overall_status === 'layak_catatan' ? 'Catatan' : 'Tidak Layak') }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($p2h->hour_meter_start, 1) }}</td>

                    {{-- Timesheet Columns --}}
                    @if($ts)
                    <td class="text-center fw-bold text-primary">{{ $ts->retase }}</td>
                    <td class="text-center">{{ number_format($ts->working_hours, 1) }} <small class="text-muted">jam</small></td>
                    <td class="text-center">{{ number_format($ts->hour_meter_end, 1) }}</td>
                    @else
                    <td class="text-center text-muted" colspan="3">
                        <small><i class="bi bi-hourglass me-1"></i>Belum ada timesheet</small>
                    </td>
                    @endif

                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('p2h.show', $p2h) }}" class="btn btn-xs btn-outline-success" title="Lihat P2H" style="padding:.2rem .4rem;font-size:.75rem;">
                                <i class="bi bi-clipboard-check"></i>
                            </a>
                            @if($ts)
                            <a href="{{ route('timesheets.show', $ts) }}" class="btn btn-xs btn-outline-secondary" title="Lihat Timesheet" style="padding:.2rem .4rem;font-size:.75rem;">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            @else
                            <a href="{{ route('timesheets.create') }}?p2h={{ $p2h->id }}" class="btn btn-xs btn-outline-primary" title="Tambah Timesheet" style="padding:.2rem .4rem;font-size:.75rem;">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data dalam rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($p2hList->hasPages())
    <div class="mt-3">{{ $p2hList->links() }}</div>
    @endif
</x-card>

{{-- Legend --}}
<div class="mt-2 d-flex gap-3 flex-wrap" style="font-size:.8rem;">
    <span><span class="badge" style="background:#1d4ed8;">FTW</span> = Fit to Work (Kesehatan Operator)</span>
    <span><span class="badge" style="background:#15803d;">P2H</span> = Pemeriksaan Kelayakan Unit</span>
    <span><span class="badge" style="background:#7c3aed;">TS</span> = Timesheet Akhir Shift (Retase & HM)</span>
</div>
@endsection
