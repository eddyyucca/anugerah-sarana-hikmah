@extends('layouts.app')
@section('page-title', 'Detail Fit to Work')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('fit-to-work.index') }}">Fit to Work</a></li>
<li class="breadcrumb-item active">{{ $fitToWork->ftw_number }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-8">
        <x-card>
            <x-slot:header>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="section-title"><i class="bi bi-heart-pulse me-2"></i>{{ $fitToWork->ftw_number }}</div>
                    <span class="badge bg-{{ $fitToWork->status_color }} fs-6 px-3">{{ $fitToWork->status_label }}</span>
                </div>
            </x-slot:header>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-semibold">{{ $fitToWork->check_date->format('d F Y') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Shift</div>
                    <span class="badge {{ $fitToWork->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                        {{ $fitToWork->shift === 'day' ? 'Shift Pagi' : 'Shift Malam' }}
                    </span>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Pemeriksa</div>
                    <div class="fw-semibold">{{ $fitToWork->checker?->name ?? 'Operator (mandiri)' }}</div>
                </div>

                <div class="col-12"><hr class="my-1"></div>

                <div class="col-md-6">
                    <div class="text-muted small">Operator</div>
                    <div class="fw-semibold">{{ $fitToWork->operator->operator_name }}</div>
                    <small class="text-muted">{{ $fitToWork->operator->operator_code }}</small>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small">Siap Bekerja?</div>
                    <span class="badge {{ $fitToWork->siap_bekerja ? 'bg-success' : 'bg-danger' }}">
                        {{ $fitToWork->siap_bekerja ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Kondisi Sehat?</div>
                    <span class="badge {{ $fitToWork->kondisi_sehat ? 'bg-success' : 'bg-danger' }}">
                        {{ $fitToWork->kondisi_sehat ? 'Ya' : 'Tidak' }}
                    </span>
                </div>

                @if($fitToWork->notes)
                <div class="col-12">
                    <div class="text-muted small">Catatan</div>
                    <div>{{ $fitToWork->notes }}</div>
                </div>
                @endif
            </div>
        </x-card>
    </div>

    <div class="col-md-4">
        <x-card class="text-center h-100 d-flex flex-column justify-content-center">
            <div style="font-size:4rem;" class="{{ $fitToWork->is_fit ? 'text-success' : 'text-danger' }}">
                <i class="bi bi-{{ $fitToWork->is_fit ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
            </div>
            <div class="fw-bold fs-4 mt-2 {{ $fitToWork->is_fit ? 'text-success' : 'text-danger' }}">
                {{ $fitToWork->is_fit ? 'FIT TO WORK' : 'TIDAK FIT' }}
            </div>
            <div class="text-muted small mt-1">
                {{ $fitToWork->is_fit ? 'Operator diizinkan bekerja' : 'Operator tidak diizinkan bekerja' }}
            </div>
        </x-card>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('fit-to-work.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
@endsection
