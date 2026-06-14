<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>P2H - Pemeriksaan Harian Unit</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @include('operator.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #dc2626; --bg: #f5f7fb; }
        * { box-sizing: border-box; }
        body { background: var(--bg); font-family: Inter, "Segoe UI", Arial, sans-serif; min-height: 100vh; }

        .p2h-header {
            background: linear-gradient(135deg, #111827, #1f2937);
            color: #fff;
            padding: 1.2rem 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .p2h-logo{
    height: 38px;
    width: auto;
    max-width: 140px;
    object-fit: contain;

    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;

    display: block;
}
        .p2h-container { max-width: 800px; margin: 0 auto; padding: 1rem; }

        .p2h-card {
            background: #fff;
            border: 1px solid #e9edf5;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(15,23,42,.06);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .p2h-card-header {
            padding: 1rem 1.2rem .5rem;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .p2h-card-body { padding: .5rem 1.2rem 1.2rem; }

        .check-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 0;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }
        .check-row:last-child { border-bottom: none; }
        .check-label { flex: 1; min-width: 160px; font-size: .88rem; font-weight: 500; }
        .check-btns { display: flex; gap: 4px; flex-shrink: 0; }
        .check-btns .btn { font-size: .72rem; padding: .3rem .55rem; border-radius: 8px; }
        .check-notes { width: 100%; margin-top: .3rem; }

        .btn-submit-p2h {
            background: linear-gradient(135deg, var(--primary), #ef4444);
            color: #fff; border: none; border-radius: 14px;
            padding: .9rem 2rem; font-weight: 700; font-size: 1rem;
            width: 100%; box-shadow: 0 6px 20px rgba(220,38,38,.3);
            transition: .2s;
        }
        .btn-submit-p2h:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(220,38,38,.4); color: #fff; }

        .success-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #86efac;
            border-radius: 18px;
            padding: 2rem;
            text-align: center;
        }
        .success-icon { font-size: 3rem; color: #16a34a; }

        .cat-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: grid; place-items: center; font-size: .9rem; flex-shrink: 0;
        }

        @media (max-width: 576px) {
            .check-row { flex-direction: column; align-items: flex-start; }
            .check-btns { width: 100%; justify-content: space-between; }
            .check-btns .btn { flex: 1; text-align: center; }
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="p2h-header">
    <div class="p2h-container p-0">
        <a href="{{ route('operator.landing') }}" style="display:flex;align-items:center;gap:.4rem;color:rgba(255,255,255,.7);text-decoration:none;font-size:.85rem;margin-bottom:.3rem;">
            <i class="bi bi-chevron-left"></i> Kembali ke Portal
        </a>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="p2h-logo">
            <div>
                <div style="font-weight:700;font-size:1.05rem;">Pemeriksaan Harian (P2H)</div>
                <div style="font-size:.78rem;color:rgba(255,255,255,.6);">APEX - Mining ERP</div>
            </div>
        </div>
    </div>
</div>

<div class="p2h-container">

    {{-- Success Message --}}
    @if(session('success'))
    <div class="success-card mb-3">
        <div class="success-icon mb-2"><i class="bi bi-check-circle-fill"></i></div>
        <div style="font-weight:700;font-size:1.2rem;color:#15803d;">{{ session('success') }}</div>
        <div class="text-muted mt-1" style="font-size:.88rem;">P2H sudah tercatat di sistem. Unit dapat dioperasikan sesuai hasil inspeksi.</div>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <a href="{{ route('p2h.form-operator') }}" class="btn btn-success" style="border-radius:12px;">
                <i class="bi bi-plus-lg me-1"></i> Isi P2H Baru
            </a>
            <a href="{{ route('operator.landing') }}" class="btn btn-outline-secondary" style="border-radius:12px;">
                <i class="bi bi-house me-1"></i> Portal
            </a>
        </div>
    </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:14px;">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('p2h.store-operator') }}" method="POST" id="p2hForm">
        @csrf

        {{-- Unit & Operator Selection --}}
        <div class="p2h-card">
            <div class="p2h-card-header"><i class="bi bi-info-circle text-primary me-1"></i> Informasi Pemeriksaan</div>
            <div class="p2h-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Pilih Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select tom-select" required style="padding:.7rem;" id="unitSelect"
                            onchange="onUnitChange(this)">
                            <option value="">-- Pilih Unit yang Akan Dipakai --</option>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}"
                                data-hm="{{ $u->hour_meter }}"
                                data-odo="{{ $u->current_odometer }}"
                                {{ old('unit_id')==$u->id?'selected':'' }}>
                                {{ $u->unit_code }} - {{ $u->unit_model }}
                            </option>
                            @endforeach
                        </select>
                        @if($units->isEmpty())
                        <div class="text-warning mt-1" style="font-size:.82rem;"><i class="bi bi-exclamation-triangle me-1"></i>Tidak ada unit available saat ini.</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nama Operator <span class="text-danger">*</span></label>
                        <select name="operator_id" class="form-select tom-select" required style="padding:.7rem;">
                            <option value="">-- Pilih Nama Anda --</option>
                            @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id')==$op->id?'selected':'' }}>{{ $op->operator_name }} ({{ $op->operator_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="check_date" class="form-control" value="{{ old('check_date', date('Y-m-d')) }}" required style="padding:.7rem;">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Shift</label>
                        <select name="shift" class="form-select tom-select" required style="padding:.7rem;">
                            <option value="day" {{ old('shift')=='day'?'selected':'' }}>Shift Pagi</option>
                            <option value="night" {{ old('shift')=='night'?'selected':'' }}>Shift Malam</option>
                        </select>
                    </div>
                    <input type="hidden" name="hour_meter_start" id="hmInput" value="0">
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            Odometer (KM) <span class="text-danger">*</span>
                            <i class="bi bi-speedometer2 ms-1 text-primary"></i>
                        </label>
                        <input type="number" step="0.1" name="km_start" id="kmInput"
                            class="form-control @error('km_start') is-invalid @enderror"
                            value="{{ old('km_start', 0) }}" min="0" required style="padding:.7rem;border-color:#3b82f6;">
                        <small id="odoHint" class="text-primary" style="font-size:.78rem;font-weight:600;"></small>
                        @error('km_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Info odometer unit saat ini --}}
                    <div class="col-12" id="odoInfoBox" style="display:none;">
                        <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #93c5fd;border-radius:12px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem;">
                            <i class="bi bi-speedometer2 text-primary" style="font-size:1.4rem;"></i>
                            <div>
                                <div style="font-size:.78rem;color:#1d4ed8;font-weight:600;">ODOMETER UNIT SAAT INI</div>
                                <div style="font-size:1.1rem;font-weight:700;color:#1e3a8a;" id="currentOdoValue">0 km</div>
                                <div style="font-size:.72rem;color:#3b82f6;">Isi dengan pembacaan odometer unit sekarang. Data ini otomatis update sistem.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checklist Per Category --}}
        @php
            $idx = 0;
            $catIcons = [
                'Engine' => ['bi-cpu', '#ef4444', 'rgba(239,68,68,.1)'],
                'Hydraulic' => ['bi-droplet-fill', '#3b82f6', 'rgba(59,130,246,.1)'],
                'Electrical' => ['bi-lightning-fill', '#f59e0b', 'rgba(245,158,11,.1)'],
                'Brake & Steering' => ['bi-disc', '#8b5cf6', 'rgba(139,92,246,.1)'],
                'Body & Cabin' => ['bi-box', '#06b6d4', 'rgba(6,182,212,.1)'],
                'Safety Equipment' => ['bi-shield-check', '#10b981', 'rgba(16,185,129,.1)'],
                'Undercarriage / Tire' => ['bi-gear-wide-connected', '#6b7280', 'rgba(107,114,128,.1)'],
            ];
        @endphp

        @foreach($checklist as $category => $items)
        @php $ci = $catIcons[$category] ?? ['bi-check-square', '#6b7280', 'rgba(107,114,128,.1)']; @endphp
        <div class="p2h-card">
            <div class="p2h-card-header">
                <div class="cat-icon" style="background:{{ $ci[2] }};color:{{ $ci[1] }};"><i class="bi {{ $ci[0] }}"></i></div>
                {{ $category }}
            </div>
            <div class="p2h-card-body">
                @foreach($items as $item)
                <div class="check-row">
                    <input type="hidden" name="items[{{ $idx }}][category]" value="{{ $category }}">
                    <input type="hidden" name="items[{{ $idx }}][check_item]" value="{{ $item }}">

                    <div class="check-label">{{ $item }}</div>

                    <div class="check-btns">
                        <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="r{{ $idx }}_g" value="good" checked>
                        <label class="btn btn-outline-success" for="r{{ $idx }}_g"><i class="bi bi-check-lg"></i> OK</label>

                        <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="r{{ $idx }}_w" value="warning">
                        <label class="btn btn-outline-warning" for="r{{ $idx }}_w"><i class="bi bi-exclamation"></i> Perlu Perhatian</label>

                        <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="r{{ $idx }}_b" value="bad">
                        <label class="btn btn-outline-danger" for="r{{ $idx }}_b"><i class="bi bi-x-lg"></i> Rusak</label>

                        <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="r{{ $idx }}_n" value="na">
                        <label class="btn btn-outline-secondary" for="r{{ $idx }}_n">N/A</label>
                    </div>

                    <div class="check-notes">
                        <input type="text" name="items[{{ $idx }}][notes]" class="form-control form-control-sm" placeholder="Catatan (opsional)..." style="font-size:.82rem;">
                    </div>
                </div>
                @php $idx++; @endphp
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- General Notes --}}
        <div class="p2h-card">
            <div class="p2h-card-header"><i class="bi bi-chat-text me-1"></i> Catatan Umum</div>
            <div class="p2h-card-body">
                <textarea name="general_notes" class="form-control" rows="3" placeholder="Tulis catatan tambahan jika ada..." style="border-radius:12px;">{{ old('general_notes') }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit-p2h mb-3" onclick="return confirm('Kirim P2H ini? Pastikan semua item sudah diperiksa.')">
            <i class="bi bi-clipboard-check me-2"></i> KIRIM P2H
        </button>

        <div class="text-center text-muted mb-2" style="font-size:.78rem;">
            <i class="bi bi-shield-check me-1"></i> Data akan tersimpan otomatis ke sistem APEX
        </div>
        <div class="text-center mb-4">
            <a href="{{ route('operator.landing') }}" style="color:#9ca3af;font-size:.83rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Portal
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function onUnitChange(sel) {
    const opt = sel.selectedOptions[0];
    const hmInput  = document.getElementById('hmInput');
    const hmHint   = document.getElementById('hmHint');
    const kmInput  = document.getElementById('kmInput');
    const odoHint  = document.getElementById('odoHint');
    const infoBox  = document.getElementById('odoInfoBox');
    const odoValue = document.getElementById('currentOdoValue');

    if (opt && opt.value) {
        const minHm  = parseFloat(opt.dataset.hm)  || 0;
        const minOdo = parseFloat(opt.dataset.odo)  || 0;

        // Hour meter
        hmInput.value = minHm;
        hmInput.min   = minHm;
        hmHint.textContent = 'HM saat ini: ' + minHm.toLocaleString('id-ID') + ' (tidak boleh lebih kecil)';

        // Odometer
        kmInput.value = minOdo;
        kmInput.min   = minOdo;
        odoHint.textContent = 'Min: ' + minOdo.toLocaleString('id-ID') + ' km';

        // Info box
        odoValue.textContent = minOdo.toLocaleString('id-ID') + ' km';
        infoBox.style.display = 'block';
    } else {
        hmInput.min = 0;
        hmHint.textContent = '';
        kmInput.min = 0;
        odoHint.textContent = '';
        infoBox.style.display = 'none';
    }
}

// Trigger saat halaman load jika ada old value
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('unitSelect');
    if (sel && sel.value) onUnitChange(sel);
});

document.getElementById('hmInput').addEventListener('input', function() {
    const opt = document.getElementById('unitSelect').selectedOptions[0];
    if (!opt || opt.dataset.hm === undefined) return;
    const minHm = parseFloat(opt.dataset.hm) || 0;
    if (parseFloat(this.value) < minHm) {
        this.setCustomValidity('HM tidak boleh kurang dari ' + minHm);
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

document.getElementById('kmInput').addEventListener('input', function() {
    const opt = document.getElementById('unitSelect').selectedOptions[0];
    if (!opt || !opt.dataset.odo) return;
    const minOdo = parseFloat(opt.dataset.odo) || 0;
    if (parseFloat(this.value) < minOdo) {
        this.setCustomValidity('Odometer tidak boleh kurang dari ' + minOdo.toLocaleString('id-ID') + ' km');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});
</script>
@include('operator.pwa-register')
</body>
</html>
