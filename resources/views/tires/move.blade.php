@extends('layouts.app')
@section('page-title', 'Pindah Ban')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('units.show', $tire->unit) }}">{{ $tire->unit->unit_code }}</a></li>
    <li class="breadcrumb-item active">Pindah Ban</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-5">
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-arrow-left-right me-2 text-warning"></i>Pindah Ban ke Unit Lain</div>
    </div>
    <div class="erp-card-body">
        <div class="alert alert-light py-2 mb-3">
            <strong>{{ $tire->sparepart->part_name ?? '-' }}</strong><br>
            <small class="text-muted">
                Saat ini: {{ $tire->unit->unit_code }} — {{ $tire->position_label }}<br>
                Total KM ban: <strong>{{ number_format($tire->current_km, 0, ',', '.') }} km</strong>
                (KM ini akan dilanjutkan di unit tujuan)
            </small>
        </div>
        <form action="{{ route('tires.move', $tire) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Unit Tujuan <span class="text-danger">*</span></label>
                <select name="unit_id" id="unitSel" class="form-select" required onchange="loadPos(this)">
                    <option value="">-- Pilih Unit --</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" data-wheel="{{ $u->wheel_count ?? 8 }}"
                        data-labels="{{ json_encode($u->wheel_position_labels) }}">
                        {{ $u->unit_code }} — {{ $u->unit_model }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Posisi Roda <span class="text-danger">*</span></label>
                <select name="position_number" id="posSel" class="form-select" required>
                    <option value="">-- Pilih unit dulu --</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Pindah <span class="text-danger">*</span></label>
                <input type="date" name="moved_at" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-warning" style="border-radius:10px;">
                    <i class="bi bi-arrow-left-right me-1"></i>Pindahkan
                </button>
                <a href="{{ route('units.show', $tire->unit) }}" class="btn btn-outline-secondary" style="border-radius:10px;">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
function loadPos(sel) {
    const opt = sel.options[sel.selectedIndex];
    const ps  = document.getElementById('posSel');
    ps.innerHTML = '<option value="">-- Pilih posisi --</option>';
    if (!opt.value) return;
    const n = parseInt(opt.dataset.wheel) || 8;
    const labels = JSON.parse(opt.dataset.labels || '{}');
    for (let i = 1; i <= n; i++) {
        const o = document.createElement('option');
        o.value = i;
        o.textContent = `#${i} — ${labels[i] || 'Posisi ' + i}`;
        ps.appendChild(o);
    }
}
</script>
@endpush
