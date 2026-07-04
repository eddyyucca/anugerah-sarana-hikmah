@extends('layouts.app')
@section('page-title', 'Mutasi Stok')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('stock-inventory.index') }}">Cek Stok</a></li>
<li class="breadcrumb-item active">Mutasi Stok</li>
@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-arrow-left-right me-2"></i>Riwayat Mutasi Stok</div>
        <a href="{{ route('stock-inventory.index') }}" class="btn btn-sm btn-light" style="border-radius:10px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Stok
        </a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Part number / nama..." value="{{ request('search') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <select name="movement_type" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Arah</option>
                    <option value="in"  {{ request('movement_type')=='in'  ? 'selected' : '' }}>Masuk (+)</option>
                    <option value="out" {{ request('movement_type')=='out' ? 'selected' : '' }}>Keluar (-)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="reference_type" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Sumber</option>
                    @foreach($referenceTypes as $rt)
                    <option value="{{ $rt }}" {{ request('reference_type')==$rt ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $rt)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-1">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('stock-inventory.movements') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Onderdil</th>
                        <th>Sumber</th>
                        <th class="text-center">Arah</th>
                        <th class="text-end">Qty Masuk</th>
                        <th class="text-end">Qty Keluar</th>
                        <th class="text-end">Saldo Akhir</th>
                        <th class="text-end">Harga/Unit</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mv)
                    <tr>
                        <td style="white-space:nowrap;font-size:.82rem;">
                            {{ $mv->movement_date instanceof \Carbon\Carbon ? $mv->movement_date->format('d/m/Y') : \Carbon\Carbon::parse($mv->movement_date)->format('d/m/Y') }}
                        </td>
                        <td>
                            @if($mv->sparepart)
                            <a href="{{ route('spareparts.show', $mv->sparepart) }}" class="text-decoration-none">
                                <div class="fw-semibold" style="font-size:.85rem;">{{ $mv->sparepart->part_number }}</div>
                                <div class="text-muted" style="font-size:.76rem;">{{ $mv->sparepart->part_name }}</div>
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-50 text-dark" style="border-radius:8px;font-size:.72rem;">
                                {{ ucfirst(str_replace('_', ' ', $mv->reference_type)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($mv->movement_type === 'in')
                                <i class="bi bi-arrow-down-circle-fill text-success fs-5" title="Masuk"></i>
                            @else
                                <i class="bi bi-arrow-up-circle-fill text-danger fs-5" title="Keluar"></i>
                            @endif
                        </td>
                        <td class="text-end fw-semibold text-success">
                            {{ $mv->qty_in > 0 ? '+'.number_format($mv->qty_in) : '-' }}
                        </td>
                        <td class="text-end fw-semibold text-danger">
                            {{ $mv->qty_out > 0 ? '-'.number_format($mv->qty_out) : '-' }}
                        </td>
                        <td class="text-end fw-bold">{{ number_format($mv->balance_after) }}</td>
                        <td class="text-end" style="font-size:.82rem;">{{ number_format($mv->unit_price, 0, ',', '.') }}</td>
                        <td style="font-size:.82rem;">{{ $mv->warehouseLocation->name ?? 'Default' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada mutasi stok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
