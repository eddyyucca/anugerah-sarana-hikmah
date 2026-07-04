@extends('layouts.app')
@section('page-title', 'Timesheet Shift')
@section('breadcrumb')
<li class="breadcrumb-item active">Timesheet</li>
@endsection

@section('content')
<x-alerts />

{{-- Filter --}}
<x-card class="mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no. TS / unit / operator..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="unit_id" class="form-select form-select-sm">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->unit_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <select name="shift" class="form-select form-select-sm">
                <option value="">Semua Shift</option>
                <option value="day" {{ request('shift') === 'day' ? 'selected' : '' }}>Pagi</option>
                <option value="night" {{ request('shift') === 'night' ? 'selected' : '' }}>Malam</option>
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-sm btn-danger"><i class="bi bi-search"></i></button>
            <a href="{{ route('timesheets.index') }}" class="btn btn-sm btn-light"><i class="bi bi-x"></i></a>
        </div>
    </form>
</x-card>

{{-- Table --}}
<x-card>
    <x-slot:header>
        <div class="d-flex justify-content-between align-items-center">
            <div class="section-title"><i class="bi bi-clock-history me-2"></i>Daftar Timesheet</div>
            <a href="{{ route('timesheets.create') }}" class="btn btn-sm btn-danger">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
        </div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.87rem;">
            <thead class="table-light">
                <tr>
                    <th>No. TS</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Unit</th>
                    <th>Operator</th>
                    <th>HM Awal</th>
                    <th>HM Akhir</th>
                    <th class="text-center">Jam Kerja</th>
                    <th class="text-center">Retase</th>
                    <th class="text-center">KM/Ritase</th>
                    <th>P2H</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($timesheets as $ts)
                <tr>
                    <td><span class="fw-semibold text-danger">{{ $ts->ts_number }}</span></td>
                    <td>{{ $ts->shift_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $ts->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                            {{ $ts->shift === 'day' ? 'Pagi' : 'Malam' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $ts->unit->unit_code }}</div>
                        <small class="text-muted">{{ $ts->unit->unit_model }}</small>
                    </td>
                    <td>{{ $ts->operator->operator_name }}</td>
                    <td>{{ number_format($ts->hour_meter_start, 1) }}</td>
                    <td>{{ number_format($ts->hour_meter_end, 1) }}</td>
                    <td class="text-center fw-semibold">{{ number_format($ts->working_hours, 1) }} <small class="text-muted fw-normal">jam</small></td>
                    <td class="text-center">
                        <span class="badge bg-primary fs-6 px-3">{{ $ts->retase }}</span>
                    </td>
                    <td class="text-center">
                        @if($ts->km_per_ritase)
                            <span class="badge {{ $ts->odo_discrepancy_flag ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ number_format($ts->km_per_ritase, 1) }} km
                            </span>
                            @if($ts->odo_discrepancy_flag)
                                <br><small class="text-danger" title="Anomali ODO"><i class="bi bi-exclamation-triangle-fill"></i> Anomali</small>
                            @endif
                        @else
                            <span class="text-muted">&mdash;</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('p2h.show', $ts->p2h_check_id) }}" class="text-decoration-none" style="font-size:.8rem;">
                            {{ $ts->p2h->p2h_number }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('timesheets.show', $ts) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center text-muted py-4">Belum ada data timesheet.</td></tr>
                @endforelse
            </tbody>
            @if($timesheets->count())
            <tfoot class="table-light">
                <tr>
                    <td colspan="8" class="text-end fw-semibold">Total halaman ini:</td>
                    <td class="text-center fw-bold">{{ number_format($timesheets->sum('working_hours'), 1) }} jam</td>
                    <td class="text-center fw-bold">{{ $timesheets->sum('retase') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($timesheets->hasPages())
    <div class="mt-3">{{ $timesheets->links() }}</div>
    @endif
</x-card>
@endsection
