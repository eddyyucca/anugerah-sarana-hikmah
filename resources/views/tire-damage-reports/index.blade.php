@extends('layouts.app')
@section('page-title', 'BA Kerusakan Ban')
@section('breadcrumb')<li class="breadcrumb-item active">BA Kerusakan Ban</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-file-earmark-text me-2 text-danger"></i>Berita Acara Kerusakan Ban</div>
        <a href="{{ route('tire-damage-reports.create') }}" class="btn btn-sm btn-danger" style="border-radius:10px;">
            <i class="bi bi-plus-lg me-1"></i>Buat BA
        </a>
    </div>
    <div class="erp-card-body">
        @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary" style="border-radius:10px;">Filter</button>
                <a href="{{ route('tire-damage-reports.index') }}" class="btn btn-sm btn-light" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>No. BA</th>
                        <th>Tanggal</th>
                        <th>Unit</th>
                        <th>Ban</th>
                        <th>Jenis Kerusakan</th>
                        <th>KM Dipakai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                    <tr>
                        <td><a href="{{ route('tire-damage-reports.show', $r) }}"><strong>{{ $r->report_no }}</strong></a></td>
                        <td>{{ $r->report_date->format('d/m/Y') }}</td>
                        <td>{{ $r->unit->unit_code }}</td>
                        <td>{{ $r->unitTire->sparepart->part_name ?? '-' }}</td>
                        <td>{{ \App\Models\TireDamageReport::damageTypeLabel($r->damage_type) }}</td>
                        <td>{{ number_format($r->km_used_when_damaged, 0, ',', '.') }} km</td>
                        <td>
                            <span class="badge bg-{{ $r->status === 'approved' ? 'success' : 'secondary' }}">
                                {{ $r->status === 'approved' ? 'Disetujui' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('tire-damage-reports.show', $r) }}" class="btn btn-xs btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada BA Kerusakan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
