@extends('layouts.app')
@section('page-title', 'Odometer')
@section('breadcrumb')<li class="breadcrumb-item active">Odometer</li>@endsection

@section('content')
<div class="row g-3">

    {{-- Input Odometer --}}
    <div class="col-lg-5">
        <div class="erp-card h-100">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-speedometer2 me-2 text-danger"></i>Catat Odometer</div>
            </div>
            <div class="erp-card-body">
                @if(session('success'))<div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
                @if(session('error'))<div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

                <form action="{{ route('odometer.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unitSelect" class="form-select @error('unit_id') is-invalid @enderror" required onchange="updateCurrentOdo(this)">
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" data-odo="{{ $unit->current_odometer }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_code }} — {{ $unit->unit_model }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Odometer Saat Ini</label>
                        <div id="currentOdoDisplay" class="alert alert-light py-2 mb-0" style="font-size:0.9rem;">
                            Pilih unit terlebih dahulu
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pembacaan Odometer Baru (km) <span class="text-danger">*</span></label>
                        <input type="number" name="odometer_km" class="form-control @error('odometer_km') is-invalid @enderror"
                            placeholder="Masukkan km baru" step="0.1" min="0" value="{{ old('odometer_km') }}" required>
                        @error('odometer_km')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pembacaan <span class="text-danger">*</span></label>
                        <input type="date" name="reading_date" class="form-control @error('reading_date') is-invalid @enderror"
                            value="{{ old('reading_date', date('Y-m-d')) }}" required>
                        @error('reading_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100" style="border-radius:10px;">
                        <i class="bi bi-save me-1"></i> Simpan Odometer
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ringkasan Odometer Unit --}}
    <div class="col-lg-7">
        <div class="erp-card h-100">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title"><i class="bi bi-list-ul me-2 text-danger"></i>Status Odometer Unit</div>
            </div>
            <div class="erp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Kode Unit</th>
                                <th>Model</th>
                                <th>Odometer (km)</th>
                                <th>Roda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                            <tr>
                                <td><strong>{{ $unit->unit_code }}</strong></td>
                                <td class="text-muted">{{ $unit->unit_model }}</td>
                                <td>
                                    <span class="fw-bold text-primary">{{ number_format($unit->current_odometer, 0, ',', '.') }} km</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $unit->wheel_count ?? 8 }} roda</span>
                                </td>
                                <td>
                                    <a href="{{ route('odometer.history', $unit) }}" class="btn btn-xs btn-outline-secondary" title="Riwayat">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada unit aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Panel --}}
    @if(count($alerts) > 0)
    <div class="col-12">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Alert Maintenance & Ban</div>
            </div>
            <div class="erp-card-body">
                <div class="row g-2">
                    @foreach($alerts as $alert)
                    <div class="col-md-6 col-lg-4">
                        <div class="alert alert-{{ $alert['severity'] }} py-2 mb-0 d-flex align-items-start gap-2">
                            <i class="bi bi-{{ $alert['severity'] === 'danger' ? 'x-circle-fill' : ($alert['severity'] === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill') }} flex-shrink-0 mt-1"></i>
                            <small>{{ $alert['message'] }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function updateCurrentOdo(select) {
    const opt = select.options[select.selectedIndex];
    const odo = opt.dataset.odo;
    const display = document.getElementById('currentOdoDisplay');
    if (odo !== undefined && opt.value) {
        display.innerHTML = `<i class="bi bi-speedometer2 me-1"></i><strong>${parseFloat(odo).toLocaleString('id-ID')} km</strong>`;
    } else {
        display.innerHTML = 'Pilih unit terlebih dahulu';
    }
}
</script>
@endpush
