@extends('layouts.app')
@section('page-title', 'Maintenance KM')
@section('breadcrumb')<li class="breadcrumb-item active">Maintenance KM</li>@endsection

@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-3">

    {{-- Summary Cards --}}
    <div class="col-md-3 col-6">
        <div class="erp-card text-center py-3">
            <div style="font-size:2rem;" class="text-danger"><i class="bi bi-x-circle-fill"></i></div>
            <div class="fw-bold fs-4 text-danger">{{ $dangerCount }}</div>
            <div class="text-muted small">Terlambat / Kritis</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="erp-card text-center py-3">
            <div style="font-size:2rem;" class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="fw-bold fs-4 text-warning">{{ $warningCount }}</div>
            <div class="text-muted small">Segera Jatuh Tempo</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="erp-card text-center py-3">
            <div style="font-size:2rem;" class="text-info"><i class="bi bi-circle-fill"></i></div>
            <div class="fw-bold fs-4">{{ $items->count() }}</div>
            <div class="text-muted small">Item Maintenance</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="erp-card text-center py-3">
            <div style="font-size:2rem;" class="text-success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="fw-bold fs-4">{{ $units->count() }}</div>
            <div class="text-muted small">Unit Aktif</div>
        </div>
    </div>

    {{-- Alert Panel --}}
    @if(count($alerts) > 0)
    <div class="col-12">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-bell-fill me-2 text-warning"></i>Alert Maintenance</div>
            </div>
            <div class="erp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Tipe</th>
                                <th>Unit</th>
                                <th>Detail</th>
                                <th>Sisa KM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $alert['severity'] }}">
                                        {{ $alert['severity'] === 'danger' ? 'KRITIS' : ($alert['severity'] === 'warning' ? 'SEGERA' : 'INFO') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alert['type'] === 'tire' ? 'dark' : 'secondary' }}">
                                        {{ $alert['type'] === 'tire' ? 'Ban' : 'Maintenance' }}
                                    </span>
                                </td>
                                <td><strong>{{ $alert['unit']->unit_code }}</strong></td>
                                <td>
                                    @if($alert['type'] === 'tire')
                                        {{ $alert['part'] }} | {{ $alert['label'] }}
                                    @else
                                        {{ $alert['label'] }}
                                    @endif
                                </td>
                                <td>
                                    @if($alert['remaining_km'] <= 0)
                                        <span class="text-danger fw-bold">Terlambat {{ number_format(abs($alert['remaining_km']), 0, ',', '.') }} km</span>
                                    @else
                                        <span class="text-{{ $alert['severity'] }}">{{ number_format($alert['remaining_km'], 0, ',', '.') }} km</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Catat Maintenance Selesai --}}
    <div class="col-lg-5">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-wrench me-2 text-danger"></i>Catat Maintenance Selesai</div>
            </div>
            <div class="erp-card-body">
                <form action="{{ route('maintenance.log') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_code }} — {{ $unit->unit_model }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Maintenance <span class="text-danger">*</span></label>
                        <select name="maintenance_item_id" class="form-select @error('maintenance_item_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('maintenance_item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} (setiap {{ number_format($item->interval_km, 0, ',', '.') }} km)
                                </option>
                            @endforeach
                        </select>
                        @error('maintenance_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="service_date" class="form-control" value="{{ old('service_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Biaya (Rp)</label>
                            <input type="number" name="cost" class="form-control" min="0" step="1000" value="{{ old('cost') }}" placeholder="Opsional">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Sparepart Digunakan</label>
                            <select name="sparepart_id" id="logSparepartId" class="form-select" onchange="updateLogQtyLabel()">
                                <option value="">-- Default dari item --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}" data-stock="{{ $sp->stock_on_hand }}" {{ old('sparepart_id') == $sp->id ? 'selected' : '' }}>
                                        {{ $sp->part_name }} (stok: {{ $sp->stock_on_hand }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Kosongkan untuk pakai sparepart default dari item maintenance.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Qty Dipakai</label>
                            <input type="number" name="qty_used" id="logQtyUsed" class="form-control" min="1" step="1" value="{{ old('qty_used') }}" placeholder="Default item">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dilakukan Oleh</label>
                            <input type="text" name="performed_by" class="form-control" value="{{ old('performed_by') }}" placeholder="Nama teknisi / vendor">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 mt-3" style="border-radius:10px;">
                        <i class="bi bi-check-circle me-1"></i> Catat Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kelola Item Maintenance --}}
    <div class="col-lg-7">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title"><i class="bi bi-gear me-2"></i>Item Maintenance</div>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#addItemModal" style="border-radius:10px;">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Item
                </button>
            </div>
            <div class="erp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Interval</th>
                                <th>Alert Sebelum</th>
                                <th>Total Log</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->sparepart)
                                        <br><small class="text-muted"><i class="bi bi-box-seam me-1"></i>{{ $item->sparepart->part_name }} × {{ $item->qty_per_service }}</small>
                                    @endif
                                </td>
                                <td>{{ number_format($item->interval_km, 0, ',', '.') }} km</td>
                                <td>{{ number_format($item->alert_before_km, 0, ',', '.') }} km sebelum</td>
                                <td><span class="badge bg-light text-dark">{{ $item->maintenance_logs_count }} log</span></td>
                                <td>
                                    <button class="btn btn-xs btn-outline-secondary me-1"
                                        onclick="editItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->interval_km }}, {{ $item->alert_before_km }}, '{{ addslashes($item->description) }}', {{ $item->sparepart_id ?? 'null' }}, {{ $item->qty_per_service ?? 1 }})"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('maintenance.items.destroy', $item) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus item ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada item maintenance. Tambahkan terlebih dahulu.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Item --}}
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="itemForm" action="{{ route('maintenance.items.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="item_id" id="itemId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Item Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Item <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="itemName" class="form-control" placeholder="Mis: Penggantian Oli, Rotasi Ban" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Interval (km) <span class="text-danger">*</span></label>
                            <input type="number" name="interval_km" id="itemInterval" class="form-control" min="100" step="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alert X km Sebelum <span class="text-danger">*</span></label>
                            <input type="number" name="alert_before_km" id="itemAlert" class="form-control" value="500" min="0" step="100" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Sparepart Default</label>
                            <select name="sparepart_id" id="itemSparepart" class="form-select">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->part_name }} (stok: {{ $sp->stock_on_hand }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Qty/Service</label>
                            <input type="number" name="qty_per_service" id="itemQty" class="form-control" min="1" step="1" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="itemDesc" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Item --}}
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editItemForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Item <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Interval (km) <span class="text-danger">*</span></label>
                            <input type="number" name="interval_km" id="editInterval" class="form-control" min="100" step="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alert X km Sebelum <span class="text-danger">*</span></label>
                            <input type="number" name="alert_before_km" id="editAlert" class="form-control" min="0" step="100" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Sparepart Default</label>
                            <select name="sparepart_id" id="editSparepart" class="form-select">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->part_name }} (stok: {{ $sp->stock_on_hand }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Qty/Service</label>
                            <input type="number" name="qty_per_service" id="editQty" class="form-control" min="1" step="1" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="editDesc" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editItem(id, name, interval, alert, desc, sparepartId, qty) {
    document.getElementById('editItemForm').action = `/maintenance/items/${id}`;
    document.getElementById('editName').value = name;
    document.getElementById('editInterval').value = interval;
    document.getElementById('editAlert').value = alert;
    document.getElementById('editDesc').value = desc || '';
    const spEl = document.getElementById('editSparepart');
    spEl.value = sparepartId || '';
    document.getElementById('editQty').value = qty || 1;
    new bootstrap.Modal(document.getElementById('editItemModal')).show();
}

function updateLogQtyLabel() {
    const sel = document.getElementById('logSparepartId');
    const opt = sel.options[sel.selectedIndex];
    const stock = opt ? opt.dataset.stock : null;
    const qtyInput = document.getElementById('logQtyUsed');
    if (stock !== undefined && stock !== null && stock !== '') {
        qtyInput.max = stock;
        qtyInput.placeholder = `Maks stok: ${stock}`;
    } else {
        qtyInput.removeAttribute('max');
        qtyInput.placeholder = 'Default item';
    }
}
</script>
@endpush
