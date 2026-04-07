@extends('layouts.app')
@section('page-title', 'Tambah Timesheet')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('timesheets.index') }}">Timesheet</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<form action="{{ route('timesheets.store') }}" method="POST" id="tsForm">
    @csrf

    {{-- Pilih P2H --}}
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-link-45deg me-2"></i>Pilih P2H (Awal Shift)</div>
        </x-slot:header>

        <div class="row g-3">
            <div class="col-md-6">
                <x-form-group label="P2H Referensi" required>
                    <select name="p2h_check_id" id="p2hSelect"
                        class="form-select tom-select @error('p2h_check_id') is-invalid @enderror" required>
                        <option value="">-- Pilih P2H --</option>
                        @foreach($p2hList as $p2h)
                        <option value="{{ $p2h->id }}"
                            data-hm="{{ $p2h->hour_meter_start }}"
                            data-unit="{{ $p2h->unit->unit_code }} - {{ $p2h->unit->unit_model }}"
                            data-operator="{{ $p2h->operator->operator_name }}"
                            data-date="{{ $p2h->check_date->format('d/m/Y') }}"
                            data-shift="{{ $p2h->shift }}"
                            data-status="{{ $p2h->overall_status }}"
                            {{ old('p2h_check_id') == $p2h->id ? 'selected' : '' }}>
                            {{ $p2h->p2h_number }} | {{ $p2h->check_date->format('d/m/Y') }} |
                            {{ $p2h->shift === 'day' ? 'Pagi' : 'Malam' }} |
                            {{ $p2h->unit->unit_code }} | {{ $p2h->operator->operator_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('p2h_check_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Hanya P2H yang belum memiliki timesheet yang ditampilkan.</small>
                </x-form-group>
            </div>

            {{-- Info otomatis dari P2H --}}
            <div class="col-md-6">
                <div id="p2hInfo" class="p-3 rounded border bg-light" style="display:none;">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <div class="text-muted">Unit</div>
                            <div class="fw-semibold" id="infoUnit">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">Operator</div>
                            <div class="fw-semibold" id="infoOperator">-</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Tanggal</div>
                            <div class="fw-semibold" id="infoDate">-</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Shift</div>
                            <div id="infoShift">-</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">HM Awal</div>
                            <div class="fw-semibold text-primary" id="infoHmStart">-</div>
                        </div>
                    </div>
                </div>
                @if($p2hList->isEmpty())
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Semua P2H sudah memiliki timesheet, atau belum ada P2H yang layak.
                </div>
                @endif
            </div>
        </div>
    </x-card>

    {{-- Data Akhir Shift --}}
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-clock-history me-2"></i>Data Akhir Shift</div>
        </x-slot:header>

        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="HM Akhir Shift" required>
                    <input type="number" step="0.1" name="hour_meter_end" id="hmEndInput"
                        class="form-control @error('hour_meter_end') is-invalid @enderror"
                        value="{{ old('hour_meter_end', 0) }}" min="0" required>
                    <small id="hmEndHint" class="text-muted"></small>
                    @error('hour_meter_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>

            <div class="col-md-3">
                <x-form-group label="Jam Kerja (otomatis)">
                    <div class="input-group">
                        <input type="text" id="workingHoursDisplay" class="form-control bg-light" readonly placeholder="0.0">
                        <span class="input-group-text">jam</span>
                    </div>
                    <small class="text-muted">Dihitung dari HM Akhir - HM Awal</small>
                </x-form-group>
            </div>

            <div class="col-md-3">
                <x-form-group label="Retase (Jumlah Trip)" required>
                    <input type="number" name="retase" class="form-control @error('retase') is-invalid @enderror"
                        value="{{ old('retase', 0) }}" min="0" required>
                    @error('retase')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>

            <div class="col-md-9">
                <x-form-group label="Catatan">
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan akhir shift...">{{ old('notes') }}</textarea>
                </x-form-group>
            </div>
        </div>
    </x-card>

    <div class="d-flex gap-2">
        <x-button type="submit" variant="danger" id="submitBtn">
            <i class="bi bi-save me-1"></i>Simpan Timesheet
        </x-button>
        <a href="{{ route('timesheets.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
let hmStart = 0;

document.getElementById('p2hSelect').addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    const info = document.getElementById('p2hInfo');

    if (opt && opt.value) {
        hmStart = parseFloat(opt.dataset.hm) || 0;
        document.getElementById('infoUnit').textContent     = opt.dataset.unit;
        document.getElementById('infoOperator').textContent = opt.dataset.operator;
        document.getElementById('infoDate').textContent     = opt.dataset.date;
        document.getElementById('infoShift').innerHTML      = opt.dataset.shift === 'day'
            ? '<span class="badge bg-warning text-dark">Pagi</span>'
            : '<span class="badge bg-secondary">Malam</span>';
        document.getElementById('infoHmStart').textContent  = hmStart.toFixed(1);

        const hmInput = document.getElementById('hmEndInput');
        hmInput.min   = hmStart;
        if (parseFloat(hmInput.value) < hmStart) hmInput.value = hmStart;
        document.getElementById('hmEndHint').textContent = 'Minimal: ' + hmStart.toFixed(1);
        updateWorkingHours();
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
        hmStart = 0;
    }
});

document.getElementById('hmEndInput').addEventListener('input', function() {
    const val = parseFloat(this.value) || 0;
    if (val < hmStart) {
        this.setCustomValidity('HM akhir tidak boleh kurang dari ' + hmStart);
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
    updateWorkingHours();
});

function updateWorkingHours() {
    const hmEnd = parseFloat(document.getElementById('hmEndInput').value) || 0;
    const hours = Math.max(0, hmEnd - hmStart);
    document.getElementById('workingHoursDisplay').value = hours.toFixed(1);
}
</script>
@endpush
