@extends('layouts.app')
@section('page-title', 'Riwayat Odometer')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('odometer.index') }}">Odometer</a></li>
    <li class="breadcrumb-item active">{{ $unit->unit_code }}</li>
@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div>
            <div class="section-title">Riwayat Odometer — {{ $unit->unit_code }}</div>
            <small class="text-muted">{{ $unit->unit_model }} | Odometer saat ini: <strong class="text-primary">{{ number_format($unit->current_odometer, 0, ',', '.') }} km</strong></small>
        </div>
        <a href="{{ route('odometer.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="erp-card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Odometer (km)</th>
                        <th>Delta (km)</th>
                        <th>Dicatat Oleh</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($readings as $r)
                    <tr>
                        <td>{{ $r->reading_date->format('d/m/Y') }}</td>
                        <td><strong>{{ number_format($r->odometer_km, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($r->delta_km > 0)
                                <span class="badge bg-success">+{{ number_format($r->delta_km, 0, ',', '.') }} km</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $r->recorded_by ?? '-' }}</td>
                        <td class="text-muted">{{ $r->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat odometer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($readings->hasPages())
        <div class="p-3">{{ $readings->links() }}</div>
        @endif
    </div>
</div>
@endsection
