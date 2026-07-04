@extends('layouts.app')
@section('page-title', 'Cek Stok Inventory')
@section('breadcrumb')<li class="breadcrumb-item active">Cek Stok</li>@endsection

@section('content')

{{-- KPI Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="erp-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;width:44px;height:44px;font-size:1.2rem;border-radius:12px;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;">Total Item</div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1.1;">{{ number_format($summary->total_items) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="erp-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;width:44px;height:44px;font-size:1.2rem;border-radius:12px;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;">Nilai Stok</div>
                    <div style="font-size:1.1rem;font-weight:800;line-height:1.1;">
                        IDR {{ number_format(($summary->total_value ?? 0) / 1e6, 1) }}Jt
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="erp-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;width:44px;height:44px;font-size:1.2rem;border-radius:12px;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;">Stok Rendah</div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1.1;color:#f59e0b;">
                        {{ number_format($summary->low_count) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="erp-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(239,68,68,.12);color:#ef4444;width:44px;height:44px;font-size:1.2rem;border-radius:12px;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;">Stok Habis</div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1.1;color:#ef4444;">
                        {{ number_format($summary->empty_count) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Tabel Stok --}}
    <div class="col-lg-8">
        <div class="erp-card h-100">
            <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="section-title"><i class="bi bi-table me-2"></i>Daftar Stok Onderdil</div>
                <div class="d-flex gap-2">
                    <a href="{{ route('stock-inventory.movements') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">
                        <i class="bi bi-arrow-left-right me-1"></i>Mutasi
                    </a>
                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-outline-primary" style="border-radius:10px;">
                        <i class="bi bi-sliders2-vertical me-1"></i>Sesuaikan
                    </a>
                    <a href="{{ route('purchase-requests.create') }}" class="btn btn-sm btn-danger" style="border-radius:10px;">
                        <i class="bi bi-file-earmark-plus me-1"></i>Buat PR
                    </a>
                </div>
            </div>
            <div class="erp-card-body">
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Part number / nama / lokasi..." value="{{ request('search') }}" style="border-radius:10px;">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select form-select-sm" style="border-radius:10px;">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="stock_status" class="form-select form-select-sm" style="border-radius:10px;">
                            <option value="">Semua Status</option>
                            <option value="ok"    {{ request('stock_status')=='ok'    ? 'selected' : '' }}>Aman</option>
                            <option value="low"   {{ request('stock_status')=='low'   ? 'selected' : '' }}>Rendah</option>
                            <option value="empty" {{ request('stock_status')=='empty' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-1">
                        <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Cari</button>
                        <a href="{{ route('stock-inventory.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Part</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min</th>
                                <th class="text-end">Nilai</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spareparts as $sp)
                            @php
                                $isEmpty = $sp->stock_on_hand == 0;
                                $isLow   = !$isEmpty && $sp->isLowStock();
                                $pct     = $sp->minimum_stock > 0
                                    ? min(100, round(($sp->stock_on_hand / ($sp->minimum_stock * 2)) * 100))
                                    : 100;
                            @endphp
                            <tr class="{{ $isEmpty ? 'table-danger' : ($isLow ? 'table-warning' : '') }}" style="opacity:{{ $isEmpty ? '.85' : '1' }};">
                                <td>
                                    <a href="{{ route('spareparts.show', $sp) }}" class="text-decoration-none">
                                        <div class="fw-semibold" style="font-size:.88rem;">{{ $sp->part_number }}</div>
                                        <div class="text-muted" style="font-size:.78rem;">{{ $sp->part_name }}</div>
                                    </a>
                                </td>
                                <td style="font-size:.82rem;">{{ $sp->category->name ?? '-' }}</td>
                                <td style="font-size:.82rem;">{{ $sp->bin_location ?: '-' }}</td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $isEmpty ? 'text-danger' : ($isLow ? 'text-warning' : 'text-success') }}"
                                          style="font-size:1rem;">{{ $sp->stock_on_hand }}</span>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $sp->uom }}</div>
                                </td>
                                <td class="text-center text-muted" style="font-size:.82rem;">{{ $sp->minimum_stock }}</td>
                                <td class="text-end" style="font-size:.82rem;">
                                    {{ number_format($sp->stock_on_hand * $sp->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($isEmpty)
                                        <span class="badge bg-danger" style="border-radius:999px;font-size:.72rem;">Habis</span>
                                    @elseif($isLow)
                                        <span class="badge bg-warning text-dark" style="border-radius:999px;font-size:.72rem;">Rendah</span>
                                    @else
                                        <span class="badge bg-success" style="border-radius:999px;font-size:.72rem;">Aman</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada onderdil ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $spareparts->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Panel Kanan: Mutasi Terbaru --}}
    <div class="col-lg-4">
        <div class="erp-card h-100">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title" style="font-size:.9rem;"><i class="bi bi-arrow-left-right me-2"></i>Mutasi Terbaru</div>
                <a href="{{ route('stock-inventory.movements') }}" class="text-muted" style="font-size:.78rem;">Lihat semua</a>
            </div>
            <div class="erp-card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentMovements as $mv)
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <div class="mt-1 flex-shrink-0">
                                @if($mv->movement_type === 'in')
                                    <i class="bi bi-arrow-down-circle-fill text-success" style="font-size:1rem;"></i>
                                @else
                                    <i class="bi bi-arrow-up-circle-fill text-danger" style="font-size:1rem;"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-semibold text-truncate" style="font-size:.82rem;">
                                    {{ $mv->sparepart->part_name ?? '-' }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" style="font-size:.74rem;">
                                        {{ ucfirst(str_replace('_', ' ', $mv->reference_type)) }}
                                    </span>
                                    <span class="fw-bold {{ $mv->movement_type==='in' ? 'text-success' : 'text-danger' }}" style="font-size:.82rem;">
                                        {{ $mv->movement_type==='in' ? '+' : '-' }}{{ $mv->movement_type==='in' ? $mv->qty_in : $mv->qty_out }}
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size:.7rem;">
                                    {{ $mv->created_at->diffForHumans() }} · Saldo: {{ $mv->balance_after }}
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4" style="font-size:.84rem;">
                        Belum ada mutasi stok.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
