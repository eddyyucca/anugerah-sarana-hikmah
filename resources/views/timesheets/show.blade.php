@extends('layouts.app')
@section('page-title', 'Detail Timesheet')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('timesheets.index') }}">Timesheet</a></li>
<li class="breadcrumb-item active">{{ $timesheet->ts_number }}</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Info Utama --}}
    <div class="col-md-8">
        <x-card class="mb-3">
            <x-slot:header>
                <div class="section-title"><i class="bi bi-clock-history me-2"></i>{{ $timesheet->ts_number }}</div>
            </x-slot:header>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Tanggal Shift</div>
                    <div class="fw-semibold">{{ $timesheet->shift_date->format('d F Y') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Shift</div>
                    <span class="badge {{ $timesheet->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                        {{ $timesheet->shift === 'day' ? 'Shift Pagi' : 'Shift Malam' }}
                    </span>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Disubmit oleh</div>
                    <div class="fw-semibold">{{ $timesheet->submitter?->name ?? '-' }}</div>
                </div>

                <div class="col-12"><hr class="my-1"></div>

                <div class="col-md-6">
                    <div class="text-muted small">Unit</div>
                    <div class="fw-semibold">{{ $timesheet->unit->unit_code }}</div>
                    <small class="text-muted">{{ $timesheet->unit->unit_model }}</small>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Operator</div>
                    <div class="fw-semibold">{{ $timesheet->operator->operator_name }}</div>
                    <small class="text-muted">{{ $timesheet->operator->operator_code }}</small>
                </div>
            </div>
        </x-card>

        {{-- P2H Referensi --}}
        <x-card>
            <x-slot:header>
                <div class="section-title"><i class="bi bi-link-45deg me-2"></i>P2H Referensi</div>
            </x-slot:header>

            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-semibold text-danger">{{ $timesheet->p2h->p2h_number }}</span>
                    <span class="ms-2 badge
                        {{ $timesheet->p2h->overall_status === 'layak' ? 'bg-success' : ($timesheet->p2h->overall_status === 'layak_catatan' ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ str_replace('_', ' ', ucfirst($timesheet->p2h->overall_status)) }}
                    </span>
                </div>
                <a href="{{ route('p2h.show', $timesheet->p2h_check_id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye me-1"></i>Lihat P2H
                </a>
            </div>
        </x-card>
    </div>

    {{-- Statistik --}}
    <div class="col-md-4">
        <x-card class="mb-3">
            <x-slot:header>
                <div class="section-title"><i class="bi bi-bar-chart me-2"></i>Hasil Shift</div>
            </x-slot:header>

            <div class="text-center py-2">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f0fdf4;">
                            <div class="fw-bold fs-4 text-success">{{ $timesheet->retase }}</div>
                            <div class="text-muted small">Retase (Trip)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#eff6ff;">
                            <div class="fw-bold fs-4 text-primary">{{ number_format($timesheet->working_hours, 1) }}</div>
                            <div class="text-muted small">Jam Kerja</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#fafaf9;">
                            <div class="fw-semibold">{{ number_format($timesheet->hour_meter_start, 1) }}</div>
                            <div class="text-muted small">HM Awal</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#fafaf9;">
                            <div class="fw-semibold">{{ number_format($timesheet->hour_meter_end, 1) }}</div>
                            <div class="text-muted small">HM Akhir</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        @if($timesheet->notes)
        <x-card>
            <x-slot:header>
                <div class="section-title"><i class="bi bi-chat-text me-2"></i>Catatan</div>
            </x-slot:header>
            <p class="mb-0">{{ $timesheet->notes }}</p>
        </x-card>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('timesheets.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
@endsection
