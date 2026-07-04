@extends('layouts.app')
@section('page-title', 'Penyesuaian Stok')
@section('breadcrumb')
<li class="breadcrumb-item active">Penyesuaian Stok</li>
@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Riwayat Penyesuaian Stok</div>
        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;">
            <i class="bi bi-plus-lg me-1"></i> Input Stok / Koreksi
        </a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari part number / nama..." value="{{ request('search') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type')=='in'?'selected':'' }}>Penambahan (+)</option>
                    <option value="out" {{ request('type')=='out'?'selected':'' }}>Pengurangan (-)</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Part Number</th>
                        <th>Nama Onderdil</th>
                        <th>Lokasi</th>
                        <th>Tipe</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Saldo Akhir</th>
                        <th class="text-end">Harga/Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr>
                        <td>{{ $adj->movement_date->format('d/m/Y') }}</td>
                        <td><strong>{{ $adj->sparepart->part_number ?? '-' }}</strong></td>
                        <td>{{ $adj->sparepart->part_name ?? '-' }}</td>
                        <td>{{ $adj->warehouseLocation->name ?? 'Default' }}</td>
                        <td>
                            @if($adj->movement_type === 'in')
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="border-radius:8px;">
                                    <i class="bi bi-plus-circle me-1"></i>Penambahan
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="border-radius:8px;">
                                    <i class="bi bi-dash-circle me-1"></i>Pengurangan
                                </span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold {{ $adj->movement_type==='in' ? 'text-success' : 'text-danger' }}">
                            {{ $adj->movement_type==='in' ? '+' : '-' }}{{ number_format($adj->movement_type==='in' ? $adj->qty_in : $adj->qty_out) }}
                        </td>
                        <td class="text-end">{{ number_format($adj->balance_after) }}</td>
                        <td class="text-end">{{ number_format($adj->unit_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada penyesuaian stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $adjustments->links() }}</div>
    </div>
</div>
@endsection
