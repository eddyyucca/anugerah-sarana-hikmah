@extends('layouts.app')
@section('page-title', 'Buat Return ke Supplier')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('supplier-returns.index') }}">Return Supplier</a></li>
    <li class="breadcrumb-item active">Buat Return</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-arrow-return-left me-2 text-warning"></i>Buat Dokumen Return ke Supplier</div>
    </div>
    <div class="erp-card-body">
        @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('supplier-returns.store') }}" method="POST" id="returnForm">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Return <span class="text-danger">*</span></label>
                    <input type="date" name="return_date" class="form-control" value="{{ old('return_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select tom-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->supplier_name }} ({{ $s->supplier_code }})
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Referensi GR (opsional)</label>
                    <select name="goods_receipt_id" class="form-select tom-select">
                        <option value="">-- Tanpa referensi GR --</option>
                        @foreach($goodsReceipts as $gr)
                        <option value="{{ $gr->id }}" {{ old('goods_receipt_id') == $gr->id ? 'selected' : '' }}>
                            {{ $gr->gr_number }} — {{ $gr->gr_date->format('d/m/Y') }} ({{ $gr->supplier->supplier_name ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Alasan Return <span class="text-danger">*</span></label>
                    <textarea name="return_reason" class="form-control @error('return_reason') is-invalid @enderror"
                        rows="2" required placeholder="Contoh: Barang cacat/rusak saat diterima, tidak sesuai spesifikasi PO, kuantitas berlebih...">{{ old('return_reason') }}</textarea>
                    @error('return_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan Tambahan</label>
                    <textarea name="notes" class="form-control" rows="1" placeholder="Opsional">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Item Lines --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-1"></i>Item yang Dikembalikan</h6>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addRow()" style="border-radius:10px;">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm" id="itemTable">
                    <thead>
                        <tr>
                            <th>Sparepart <span class="text-danger">*</span></th>
                            <th style="width:100px;">Qty Return <span class="text-danger">*</span></th>
                            <th>Alasan Cacat <span class="text-danger">*</span></th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <tr id="row-0">
                            <td>
                                <select name="items[0][sparepart_id]" class="form-select form-select-sm tom-select" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->part_name }} @if($sp->part_number)({{ $sp->part_number }})@endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="items[0][qty_returned]"
                                    class="form-control form-control-sm" placeholder="0" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][defect_reason]"
                                    class="form-control form-control-sm" placeholder="Cacat, salah tipe, dsb." required maxlength="200">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-warning" style="border-radius:10px;">
                    <i class="bi bi-save me-1"></i>Simpan Draft
                </button>
                <a href="{{ route('supplier-returns.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@push('scripts')
<script>
let rowCount = 1;
const spareparts = @json($spareparts->map(fn($s) => ['id'=>$s->id, 'name'=>$s->part_name.($s->part_number?' ('.$s->part_number.')':'')]));

function addRow() {
    const idx = rowCount++;
    const opts = spareparts.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    const row = `<tr id="row-${idx}">
        <td><select name="items[${idx}][sparepart_id]" class="form-select form-select-sm" required>
            <option value="">-- Pilih --</option>${opts}</select></td>
        <td><input type="number" step="0.01" min="0.01" name="items[${idx}][qty_returned]" class="form-control form-control-sm" placeholder="0" required></td>
        <td><input type="text" name="items[${idx}][defect_reason]" class="form-control form-control-sm" placeholder="Cacat, salah tipe, dsb." required maxlength="200"></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-x"></i></button></td>
    </tr>`;
    document.getElementById('itemBody').insertAdjacentHTML('beforeend', row);
}
</script>
@endpush
@endsection
