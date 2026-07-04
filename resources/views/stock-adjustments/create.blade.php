@extends('layouts.app')
@section('page-title', 'Input Stok / Koreksi')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('stock-adjustments.index') }}">Penyesuaian Stok</a></li>
<li class="breadcrumb-item active">Input Baru</li>
@endsection

@section('content')
<form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjForm">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">Informasi Penyesuaian</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="Tanggal" required>
                    <input type="date" name="adjustment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-9">
                <x-form-group label="Alasan / Keterangan" required>
                    <input type="text" name="reason" class="form-control" placeholder="Contoh: Stok awal, koreksi opname, temuan gudang..." required>
                </x-form-group>
            </div>
        </div>
        <div class="alert alert-info d-flex gap-2 mt-3 mb-0" style="border-radius:10px;font-size:.84rem;">
            <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
            <div>
                Gunakan <strong>Qty positif (+)</strong> untuk menambah stok (input barang baru, koreksi lebih).
                Gunakan <strong>Qty negatif (-)</strong> untuk mengurangi stok (koreksi kurang, barang rusak/hilang).
            </div>
        </div>
    </x-card>

    <x-card class="mb-3">
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="section-title">Daftar Onderdil</div>
                <button type="button" class="btn btn-outline-danger btn-sm" style="border-radius:10px;" onclick="addRow()">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Item
                </button>
            </div>
        </x-slot:header>

        @if($errors->any())
        <div class="alert alert-danger mx-3 mt-3" style="border-radius:10px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-modern mb-0" id="adjTable">
                <thead>
                    <tr>
                        <th>Onderdil</th>
                        <th style="width:160px;">Lokasi Gudang</th>
                        <th style="width:110px;">Qty <small class="text-muted fw-normal">(+/-)</small></th>
                        <th style="width:140px;">Harga/Unit</th>
                        <th style="width:200px;">Catatan</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody id="adjBody"></tbody>
            </table>
        </div>
    </x-card>

    <button type="submit" class="btn btn-danger" style="border-radius:10px;">
        <i class="bi bi-check-lg me-1"></i>Simpan Penyesuaian
    </button>
    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-light ms-2" style="border-radius:10px;">Batal</a>
</form>
@endsection

@push('scripts')
<script>
const spareparts = @json($spareparts);
const locations  = @json($locations);
let rowIdx = 0;

function addRow(sparepartId = '', qty = 1, price = '') {
    const spOpts = spareparts.map(sp =>
        `<option value="${sp.id}" data-price="${sp.unit_price}" ${sp.id == sparepartId ? 'selected' : ''}>
            ${sp.part_number} — ${sp.part_name} (Stok: ${sp.stock_on_hand} ${sp.uom})
        </option>`
    ).join('');
    const locOpts = locations.map(l => `<option value="${l.id}">${l.name}</option>`).join('');

    const idx = rowIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="items[${idx}][sparepart_id]" class="form-select form-select-sm tom-select sp-select" required onchange="fillPrice(this, ${idx})">
                <option value="">-- Pilih Onderdil --</option>
                ${spOpts}
            </select>
        </td>
        <td>
            <select name="items[${idx}][warehouse_location_id]" class="form-select form-select-sm">
                <option value="">Default</option>
                ${locOpts}
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm text-end"
                   value="${qty}" required placeholder="0" style="font-weight:600;">
        </td>
        <td>
            <input type="number" name="items[${idx}][unit_price]" id="price_${idx}" class="form-control form-control-sm text-end"
                   value="${price}" placeholder="Otomatis" min="0" step="100">
        </td>
        <td>
            <input type="text" name="items[${idx}][notes]" class="form-control form-control-sm" placeholder="Opsional">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove()" title="Hapus">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;
    document.getElementById('adjBody').appendChild(tr);

    // Init tom-select on new row
    if (window.TomSelect) {
        tr.querySelectorAll('.tom-select').forEach(el => {
            if (!el.tomSelect) new TomSelect(el, {});
        });
    }
}

function fillPrice(select, idx) {
    const opt = select.options[select.selectedIndex];
    const price = opt ? opt.getAttribute('data-price') : '';
    const priceInput = document.getElementById(`price_${idx}`);
    if (priceInput && !priceInput.value) {
        priceInput.value = price || '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    addRow();
});
</script>
@endpush
