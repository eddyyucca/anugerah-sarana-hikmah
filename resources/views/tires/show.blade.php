@extends('layouts.app')
@section('page-title', 'Detail Ban')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tires.index') }}">Ban</a></li>
    <li class="breadcrumb-item active">{{ $tire->sparepart->part_name ?? 'Ban #'.$tire->id }}</li>
@endsection

@section('content')
@php
    $pct = $tire->usage_percent;
    $bar = $pct >= 100 ? 'danger' : ($pct >= 85 ? 'warning' : ($pct >= 60 ? 'info' : 'success'));
@endphp

<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-body">
                <div class="text-center mb-3">
                    <div style="font-size:3rem;color:#dc3545;"><i class="bi bi-circle-fill"></i></div>
                    <h5 class="fw-bold mb-0">{{ $tire->sparepart->part_name ?? '-' }}</h5>
                    <small class="text-muted">{{ $tire->sparepart->part_number ?? '' }}</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Pemakaian</span><span>{{ $pct }}%</span>
                    </div>
                    <div class="progress" style="height:12px;border-radius:6px;">
                        <div class="progress-bar bg-{{ $bar }}" style="width:{{ min(100,$pct) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small mt-1 text-muted">
                        <span>{{ number_format($tire->current_km, 0, ',', '.') }} km</span>
                        <span>Batas: {{ number_format($tire->km_limit, 0, ',', '.') }} km</span>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-0">
                    @if($tire->serial_number)
                    <tr><td class="text-muted">No. Seri</td>
                        <td><code>{{ $tire->serial_number }}</code></td></tr>
                    @endif
                    <tr><td class="text-muted">Sisa KM</td>
                        <td><strong class="text-{{ $bar }}">{{ number_format($tire->remaining_km, 0, ',', '.') }} km</strong></td></tr>
                    <tr><td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $tire->is_installed ? 'success' : 'secondary' }}">{{ $tire->is_installed ? 'Terpasang' : 'Gudang' }}</span></td></tr>
                    @if($tire->is_installed)
                    <tr><td class="text-muted">Unit</td>
                        <td><a href="{{ route('units.show', $tire->unit) }}"><strong>{{ $tire->unit->unit_code }}</strong></a></td></tr>
                    <tr><td class="text-muted">Posisi</td>
                        <td>{{ $tire->position_label }}</td></tr>
                    @endif
                </table>

                @if($tire->notes)
                <div class="alert alert-light py-2 mt-2"><small>{{ $tire->notes }}</small></div>
                @endif

                <div class="mt-3 d-flex flex-column gap-2">
                    <a href="{{ route('tire-damage-reports.create', ['unit_tire_id' => $tire->id]) }}"
                       class="btn btn-sm btn-outline-danger" style="border-radius:10px;">
                        <i class="bi bi-file-earmark-text me-1"></i>Buat BA Kerusakan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat Pemasangan</div>
            </div>
            <div class="erp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Unit</th><th>Posisi</th><th>Pasang</th><th>Lepas</th><th>KM Digunakan</th><th>Alasan</th></tr>
                        </thead>
                        <tbody>
                            @forelse($tire->histories as $h)
                            <tr>
                                <td><strong>{{ $h->unit->unit_code }}</strong></td>
                                <td>{{ $h->position_label }}</td>
                                <td>{{ $h->installed_at->format('d/m/Y') }}</td>
                                <td>{!! $h->removed_at?->format('d/m/Y') ?? '<span class="text-success">Aktif</span>' !!}</td>
                                <td>{{ number_format($h->km_used, 0, ',', '.') }} km</td>
                                <td class="text-muted">{{ $h->removed_reason ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
