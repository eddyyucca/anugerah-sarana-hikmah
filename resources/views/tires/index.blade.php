@extends('layouts.app')
@section('page-title', 'Ban')
@section('breadcrumb')<li class="breadcrumb-item active">Ban</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-circle me-2 text-danger"></i>Semua Ban Terlacak</div>
        <a href="{{ route('tires.analytics') }}" class="btn btn-sm btn-outline-primary" style="border-radius:10px;">
            <i class="bi bi-graph-up me-1"></i>Analitik Lifetime
        </a>
    </div>
    <div class="erp-card-body">
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Status</option>
                    <option value="terpasang" {{ request('status')=='terpasang'?'selected':'' }}>Terpasang</option>
                    <option value="gudang"    {{ request('status')=='gudang'?'selected':'' }}>Gudang</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="unit_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Unit</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('tires.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr><th>Ban (Sparepart)</th><th>Unit</th><th>Posisi</th><th>Total KM</th><th>Batas KM</th><th>Progress</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($tires as $tire)
                    @php
                        $pct = $tire->usage_percent;
                        $bar = $pct >= 100 ? 'danger' : ($pct >= 85 ? 'warning' : ($pct >= 60 ? 'info' : 'success'));
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('tires.show', $tire) }}" class="text-decoration-none fw-semibold">
                                {{ $tire->sparepart->part_name ?? '-' }}
                            </a>
                            <div class="text-muted" style="font-size:.78rem;">{{ $tire->sparepart->part_number ?? '' }}</div>
                        </td>
                        <td>
                            @if($tire->unit)
                            <a href="{{ route('units.show', $tire->unit) }}" class="text-decoration-none">{{ $tire->unit->unit_code }}</a>
                            @else
                            <span class="text-muted">Gudang</span>
                            @endif
                        </td>
                        <td>{{ $tire->position_label ?? '—' }}</td>
                        <td><strong>{{ number_format($tire->current_km, 0, ',', '.') }} km</strong></td>
                        <td class="text-muted">{{ number_format($tire->km_limit, 0, ',', '.') }} km</td>
                        <td style="min-width:120px;">
                            <div class="d-flex align-items-center gap-1">
                                <div class="progress flex-grow-1" style="height:7px;border-radius:4px;">
                                    <div class="progress-bar bg-{{ $bar }}" style="width:{{ min(100,$pct) }}%"></div>
                                </div>
                                <small class="text-{{ $bar }}">{{ $pct }}%</small>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('tires.show', $tire) }}" class="btn btn-xs btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data ban.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tires->hasPages())
        <div class="mt-3">{{ $tires->links() }}</div>
        @endif
    </div>
</div>
@endsection
