@extends('layouts.app')
@section('page-title', 'Buat BA Kerusakan Ban')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tire-damage-reports.index') }}">BA Kerusakan Ban</a></li>
    <li class="breadcrumb-item active">Buat BA</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-file-earmark-text me-2 text-danger"></i>Berita Acara Kerusakan Ban</div>
    </div>
    <div class="erp-card-body">
        @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('tire-damage-reports.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal BA <span class="text-danger">*</span></label>
                    <input type="date" name="report_date" class="form-control" value="{{ old('report_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select tom-select @error('unit_id') is-invalid @enderror" required id="unitSelect" onchange="filterTires(this.value)">
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id', $unitTire?->unit_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->unit_code }} — {{ $u->unit_model }} (ODO: {{ number_format($u->current_odometer, 0, ',', '.') }} km)
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Ban yang Rusak <span class="text-danger">*</span></label>
                    <select name="unit_tire_id" class="form-select tom-select @error('unit_tire_id') is-invalid @enderror" required id="tireSelect">
                        <option value="">-- Pilih Ban --</option>
                        @foreach($tires as $t)
                        <option value="{{ $t->id }}"
                            data-unit="{{ $t->unit_id }}"
                            {{ old('unit_tire_id', $unitTire?->id) == $t->id ? 'selected' : '' }}>
                            {{ $t->sparepart->part_name ?? 'Ban #'.$t->id }}
                            @if($t->serial_number) [SN: {{ $t->serial_number }}] @endif
                            — Pos. {{ $t->position_label }}
                            ({{ number_format($t->total_km, 0, ',', '.') }} km terpakai)
                        </option>
                        @endforeach
                    </select>
                    @error('unit_tire_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis Kerusakan <span class="text-danger">*</span></label>
                    <select name="damage_type" class="form-select @error('damage_type') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="puncture" {{ old('damage_type')=='puncture'?'selected':'' }}>Bocor (Puncture)</option>
                        <option value="sidewall" {{ old('damage_type')=='sidewall'?'selected':'' }}>Dinding Robek (Sidewall)</option>
                        <option value="bead" {{ old('damage_type')=='bead'?'selected':'' }}>Bead Rusak</option>
                        <option value="tread" {{ old('damage_type')=='tread'?'selected':'' }}>Tapak Aus (Tread)</option>
                        <option value="manufacturing_defect" {{ old('damage_type')=='manufacturing_defect'?'selected':'' }}>Cacat Produksi</option>
                        <option value="other" {{ old('damage_type','other')=='other'?'selected':'' }}>Lainnya</option>
                    </select>
                    @error('damage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Klaim Garansi?</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_warranty_claim" value="1" class="form-check-input"
                            id="warrantyCheck" {{ old('is_warranty_claim') ? 'checked' : '' }}>
                        <label class="form-check-label" for="warrantyCheck">Ya, akan diklaim garansi ke supplier/produsen</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                    <textarea name="damage_description" class="form-control @error('damage_description') is-invalid @enderror"
                        rows="3" required placeholder="Jelaskan kondisi kerusakan secara detail...">{{ old('damage_description') }}</textarea>
                    @error('damage_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan Tambahan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Opsional">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-danger" style="border-radius:10px;">
                    <i class="bi bi-save me-1"></i>Buat BA Kerusakan
                </button>
                <a href="{{ route('tire-damage-reports.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@push('scripts')
<script>
function filterTires(unitId) {
    const tireSelect = document.getElementById('tireSelect');
    const options = tireSelect.querySelectorAll('option[data-unit]');
    options.forEach(opt => {
        opt.hidden = unitId && opt.dataset.unit !== unitId;
    });
    tireSelect.value = '';
}
document.addEventListener('DOMContentLoaded', () => {
    const uid = document.getElementById('unitSelect').value;
    if (uid) filterTires(uid);
});
</script>
@endpush
@endsection
