@extends('layouts.app')
@section('page-title', $tireDamageReport->report_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tire-damage-reports.index') }}">BA Kerusakan Ban</a></li>
    <li class="breadcrumb-item active">{{ $tireDamageReport->report_no }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-body">
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem;color:#dc3545;"><i class="bi bi-file-earmark-text"></i></div>
                    <h5 class="fw-bold mb-0">{{ $tireDamageReport->report_no }}</h5>
                    <span class="badge bg-{{ $tireDamageReport->status === 'approved' ? 'success' : 'secondary' }} mt-1">
                        {{ $tireDamageReport->status === 'approved' ? 'Disetujui' : 'Draft' }}
                    </span>
                    @if($tireDamageReport->is_warranty_claim)
                    <span class="badge bg-warning text-dark mt-1">Klaim Garansi</span>
                    @endif
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Tanggal</td><td>{{ $tireDamageReport->report_date->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted">Unit</td>
                        <td><a href="{{ route('units.show', $tireDamageReport->unit) }}"><strong>{{ $tireDamageReport->unit->unit_code }}</strong></a></td>
                    </tr>
                    <tr><td class="text-muted">Ban</td>
                        <td>
                            <a href="{{ route('tires.show', $tireDamageReport->unitTire) }}">
                                {{ $tireDamageReport->unitTire->sparepart->part_name ?? '-' }}
                            </a>
                            @if($tireDamageReport->unitTire->serial_number)
                            <br><small class="text-muted">SN: <code>{{ $tireDamageReport->unitTire->serial_number }}</code></small>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="text-muted">Terpasang</td><td>{{ $tireDamageReport->installed_at?->format('d/m/Y') ?? '-' }}</td></tr>
                    <tr><td class="text-muted">KM Saat Rusak</td><td>{{ number_format($tireDamageReport->km_at_damage, 0, ',', '.') }} km</td></tr>
                    <tr><td class="text-muted">KM Dipakai</td><td><strong class="text-danger">{{ number_format($tireDamageReport->km_used_when_damaged, 0, ',', '.') }} km</strong></td></tr>
                    <tr><td class="text-muted">Jenis</td><td>{{ \App\Models\TireDamageReport::damageTypeLabel($tireDamageReport->damage_type) }}</td></tr>
                </table>

                @if($tireDamageReport->approved_by)
                <div class="alert alert-success py-2 mt-2">
                    <small><strong>Disetujui oleh:</strong> {{ $tireDamageReport->approved_by }}<br>
                    {{ $tireDamageReport->approved_at?->format('d/m/Y H:i') }}</small>
                </div>
                @endif

                <div class="d-flex flex-column gap-2 mt-3">
                    @if($tireDamageReport->status === 'draft')
                    <form action="{{ route('tire-damage-reports.approve', $tireDamageReport) }}" method="POST"
                        onsubmit="return confirm('Setujui BA Kerusakan ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" style="border-radius:10px;">
                            <i class="bi bi-check-lg me-1"></i>Setujui BA
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('print.ba-damage', $tireDamageReport) }}" target="_blank"
                       class="btn btn-outline-secondary w-100" style="border-radius:10px;">
                        <i class="bi bi-printer me-1"></i>Print BA
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success py-2 mt-3">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger py-2 mt-3">{{ session('error') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Detail Kerusakan</div>
            </div>
            <div class="erp-card-body">
                <div class="mb-3">
                    <label class="text-muted small">Deskripsi Kerusakan</label>
                    <div class="p-3 bg-light rounded-3 mt-1">{{ $tireDamageReport->damage_description }}</div>
                </div>
                @if($tireDamageReport->notes)
                <div>
                    <label class="text-muted small">Catatan</label>
                    <div class="p-3 bg-light rounded-3 mt-1">{{ $tireDamageReport->notes }}</div>
                </div>
                @endif

                {{-- Info ban --}}
                <hr>
                <h6 class="fw-semibold mb-2">Info Ban</h6>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Nama Ban</td><td>{{ $tireDamageReport->unitTire->sparepart->part_name ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Part Number</td><td>{{ $tireDamageReport->unitTire->sparepart->part_number ?? '-' }}</td></tr>
                    <tr><td class="text-muted">KM Limit</td><td>{{ number_format($tireDamageReport->unitTire->km_limit, 0, ',', '.') }} km</td></tr>
                    <tr><td class="text-muted">KM Rusak / Limit</td>
                        <td>
                            <strong>{{ number_format($tireDamageReport->km_used_when_damaged, 0, ',', '.') }}</strong>
                            / {{ number_format($tireDamageReport->unitTire->km_limit, 0, ',', '.') }} km
                            @php $pct = $tireDamageReport->km_used_when_damaged > 0 ? min(100, round(($tireDamageReport->km_used_when_damaged / $tireDamageReport->unitTire->km_limit) * 100)) : 0; @endphp
                            <span class="badge bg-{{ $pct < 30 ? 'danger' : ($pct < 70 ? 'warning' : 'success') }} ms-1">
                                {{ $pct }}% dari limit
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
