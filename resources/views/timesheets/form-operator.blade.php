<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet Akhir Shift - APEX ERP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @include('operator.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --primary: #7c3aed; --bg: #faf5ff; }
        body { background: var(--bg); font-family: Inter, "Segoe UI", Arial, sans-serif; min-height: 100vh; }

        .ts-header {
            background: linear-gradient(135deg, #4c1d95, #7c3aed);
            color: #fff;
            padding: 1.2rem 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .ts-logo { height: 38px; width: auto; object-fit: contain; }
        .ts-container { max-width: 560px; margin: 0 auto; padding: 1rem; }

        .ts-card {
            background: #fff;
            border: 1px solid #ede9fe;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(124,58,237,.08);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .ts-card-header {
            padding: 1rem 1.2rem .5rem;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #4c1d95;
        }
        .ts-card-body { padding: .5rem 1.2rem 1.2rem; }

        .p2h-option-card {
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: .8rem 1rem;
            margin-bottom: .5rem;
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: .8rem;
        }
        .p2h-option-card:has(input:checked) {
            border-color: #7c3aed;
            background: #f5f3ff;
        }
        .p2h-option-card input[type=radio] { accent-color: #7c3aed; width: 18px; height: 18px; flex-shrink: 0; }
        .p2h-badge { font-size: .72rem; }

        .hm-display {
            background: #f5f3ff;
            border: 1px solid #c4b5fd;
            border-radius: 12px;
            padding: .8rem 1rem;
            margin-bottom: .8rem;
        }

        .stat-box {
            background: #f5f3ff;
            border-radius: 12px;
            padding: .8rem 1rem;
            text-align: center;
        }
        .stat-val { font-weight: 700; font-size: 1.4rem; color: #7c3aed; }
        .stat-label { font-size: .75rem; color: #6b7280; }

        .btn-submit-ts {
            background: linear-gradient(135deg, #7c3aed, #8b5cf6);
            color: #fff; border: none; border-radius: 14px;
            padding: .9rem 2rem; font-weight: 700; font-size: 1rem;
            width: 100%; box-shadow: 0 6px 20px rgba(124,58,237,.3);
            transition: .2s;
        }
        .btn-submit-ts:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(124,58,237,.4); color: #fff; }

        .success-card {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border: 2px solid #c4b5fd;
            border-radius: 18px;
            padding: 2rem;
            text-align: center;
        }
        .back-link {
            display: flex; align-items: center; gap: .4rem;
            color: rgba(255,255,255,.7); text-decoration: none;
            font-size: .85rem; margin-bottom: .3rem;
        }
        .back-link:hover { color: #fff; }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
        }

        /* Foto ODO */
        .foto-upload-wrap {
            border: 2px dashed #c4b5fd;
            border-radius: 14px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #faf5ff;
            position: relative;
        }
        .foto-upload-wrap:hover { border-color: #7c3aed; background: #f5f3ff; }
        .foto-upload-wrap input[type=file] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .foto-preview {
            width: 100%; max-height: 200px; object-fit: contain;
            border-radius: 10px; margin-top: .6rem; display: none;
        }
        .foto-upload-label { font-size: .82rem; color: #7c3aed; font-weight: 600; pointer-events: none; }

        /* Warning anomali */
        .anomali-warning {
            background: #fff7ed;
            border: 1.5px solid #f97316;
            border-radius: 12px;
            padding: .7rem 1rem;
            font-size: .82rem;
            color: #9a3412;
            display: none;
        }
        .anomali-ok {
            background: #f0fdf4;
            border: 1.5px solid #22c55e;
            border-radius: 12px;
            padding: .7rem 1rem;
            font-size: .82rem;
            color: #14532d;
            display: none;
        }
    </style>
</head>
<body>

<div class="ts-header">
    <div class="ts-container p-0">
        <a href="{{ route('operator.landing') }}" class="back-link">
            <i class="bi bi-chevron-left"></i> Kembali ke Portal
        </a>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="ts-logo">
            <div>
                <div style="font-weight:700;font-size:1.05rem;">Timesheet Akhir Shift</div>
                <div style="font-size:.78rem;color:rgba(255,255,255,.6);">Input HM Akhir & Retase</div>
            </div>
        </div>
    </div>
</div>

<div class="ts-container">

    @if(session('success'))
    <div class="success-card mb-3">
        <div style="font-size:3.5rem;color:#7c3aed;" class="mb-2"><i class="bi bi-check-circle-fill"></i></div>
        <div style="font-weight:700;font-size:1.2rem;color:#4c1d95;">Timesheet Tersimpan!</div>
        <div class="text-muted mt-1" style="font-size:.88rem;">Data shift Anda sudah tercatat di sistem.</div>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <a href="{{ route('operator.ts-form') }}" class="btn btn-outline-secondary" style="border-radius:12px;">
                Isi Timesheet Lagi
            </a>
            <a href="{{ route('operator.landing') }}" class="btn" style="background:#7c3aed;color:#fff;border-radius:12px;">
                <i class="bi bi-house me-1"></i>Portal
            </a>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:14px;">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @if($p2hList->isEmpty())
    <div class="ts-card">
        <div class="ts-card-body empty-state">
            <i class="bi bi-inbox" style="font-size:3rem;color:#c4b5fd;"></i>
            <div class="mt-2 fw-semibold" style="color:#4c1d95;">Tidak Ada P2H Tersedia</div>
            <div class="mt-1" style="font-size:.85rem;">Semua P2H sudah memiliki timesheet, atau belum ada P2H yang layak hari ini.</div>
            <a href="{{ route('p2h.form-operator') }}" class="btn mt-3" style="background:#7c3aed;color:#fff;border-radius:12px;">
                <i class="bi bi-clipboard-check me-1"></i>Isi P2H Terlebih Dahulu
            </a>
        </div>
    </div>
    @else
    <form action="{{ route('operator.ts-store') }}" method="POST" id="tsForm" enctype="multipart/form-data">
        @csrf

        {{-- Pilih P2H --}}
        <div class="ts-card">
            <div class="ts-card-header"><i class="bi bi-link-45deg"></i> Pilih P2H Shift Anda</div>
            <div class="ts-card-body">
                @foreach($p2hList as $p2h)
                <label class="p2h-option-card" onclick="selectP2H(this)">
                    <input type="radio" name="p2h_check_id" value="{{ $p2h->id }}"
                        data-km="{{ $p2h->km_start }}"
                        data-unit="{{ $p2h->unit->unit_code }} — {{ $p2h->unit->unit_model }}"
                        data-operator="{{ $p2h->operator->operator_name }}"
                        data-date="{{ $p2h->check_date->format('d/m/Y') }}"
                        data-shift="{{ $p2h->shift }}"
                        {{ old('p2h_check_id') == $p2h->id ? 'checked' : '' }}
                        onchange="onP2HChange(this)" required>
                    <div style="flex:1;">
                        <div class="fw-semibold" style="font-size:.9rem;">
                            {{ $p2h->p2h_number }}
                            <span class="badge ms-1 p2h-badge {{ $p2h->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                {{ $p2h->shift === 'day' ? 'Pagi' : 'Malam' }}
                            </span>
                            <span class="badge ms-1 p2h-badge bg-success">{{ str_replace('_',' ',ucfirst($p2h->overall_status)) }}</span>
                        </div>
                        <div style="font-size:.78rem;color:#6b7280;margin-top:.2rem;">
                            {{ $p2h->unit->unit_code }} &bull; {{ $p2h->operator->operator_name }} &bull; {{ $p2h->check_date->format('d/m/Y') }}
                        </div>
                        <div style="font-size:.78rem;color:#3b82f6;font-weight:600;">
                            <i class="bi bi-speedometer2 me-1"></i>ODO Awal: {{ number_format($p2h->km_start, 0, ',', '.') }} km
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Info P2H terpilih --}}
        <div id="p2hInfoBox" style="display:none;">
            <div class="ts-card">
                <div class="ts-card-header"><i class="bi bi-info-circle"></i> Detail Shift Terpilih</div>
                <div class="ts-card-body">
                    <div class="hm-display">
                        <div class="row text-center">
                            <div class="col-4">
                                <div style="font-size:.72rem;color:#6b7280;">Unit</div>
                                <div id="infoUnit" class="fw-semibold" style="font-size:.85rem;"></div>
                            </div>
                            <div class="col-4">
                                <div style="font-size:.72rem;color:#6b7280;">Operator</div>
                                <div id="infoOperator" class="fw-semibold" style="font-size:.85rem;"></div>
                            </div>
                            <div class="col-4">
                                <div style="font-size:.72rem;color:#6b7280;"><i class="bi bi-speedometer2"></i> ODO Awal</div>
                                <div id="infoKmStart" class="fw-bold" style="font-size:1rem;color:#3b82f6;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input akhir shift --}}
            <div class="ts-card">
                <div class="ts-card-header"><i class="bi bi-clock-history"></i> Data Akhir Shift</div>
                <div class="ts-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="bi bi-speedometer2 me-1 text-primary"></i>Odometer Akhir Shift (KM) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.1" name="km_end" id="kmEndInput"
                                class="form-control @error('km_end') is-invalid @enderror"
                                value="{{ old('km_end', 0) }}" min="0" required
                                style="padding:.8rem;border-radius:12px;font-size:1.1rem;font-weight:700;border-color:#3b82f6;"
                                oninput="updateCalc()">
                            <small id="kmEndHint" class="text-muted"></small>
                            @error('km_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Jam Kerja Shift <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.5" name="working_hours" id="workingHoursInput"
                                    class="form-control @error('working_hours') is-invalid @enderror"
                                    value="{{ old('working_hours', 8) }}" min="0" max="24" required
                                    style="padding:.8rem;border-radius:12px 0 0 12px;font-size:1rem;font-weight:600;">
                                <span class="input-group-text">jam</span>
                            </div>
                            @error('working_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Preview kalkulasi --}}
                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div class="stat-val" id="kmTraveledDisp" style="color:#3b82f6;">0</div>
                                        <div class="stat-label">KM Ditempuh</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div>
                                            <input type="number" name="retase" id="retaseInput"
                                                class="form-control @error('retase') is-invalid @enderror text-center"
                                                value="{{ old('retase', 0) }}" min="0" required
                                                oninput="updateCalc()"
                                                style="font-weight:700;font-size:1.4rem;color:#7c3aed;border:none;background:transparent;padding:0;text-align:center;width:100%;">
                                        </div>
                                        <div class="stat-label">Ritase (Trip)</div>
                                        @error('retase')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div class="stat-val" id="kmPerRitaseDisp" style="color:#059669;">—</div>
                                        <div class="stat-label">KM/Ritase</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Indikator validasi ODO vs Ritase --}}
                        <div class="col-12">
                            <div class="anomali-warning" id="anomaliWarning">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Peringatan Anomali ODO!</strong>
                                <span id="anomaliMsg"></span>
                                <br><small>Mohon periksa kembali data odometer dan jumlah ritase Anda.</small>
                            </div>
                            <div class="anomali-ok" id="anomaliOk">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Data ODO dan ritase <strong>tampak normal</strong>.
                                <span id="anomaliOkMsg"></span>
                            </div>
                        </div>

                        {{-- Foto ODO Akhir --}}
                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="bi bi-camera me-1"></i>Foto Odometer Akhir Shift</label>
                            <div class="foto-upload-wrap" onclick="triggerFile('odo_end_photo')">
                                <input type="file" name="odo_end_photo" id="odo_end_photo" accept="image/*" capture="environment"
                                    onchange="previewFoto(this, 'previewAkhir', 'fotoAkhirLabel')">
                                <i class="bi bi-camera-fill" style="font-size:2rem;color:#c4b5fd;"></i>
                                <div class="foto-upload-label mt-1" id="fotoAkhirLabel">Tap untuk ambil / pilih foto ODO akhir</div>
                                <img id="previewAkhir" class="foto-preview" alt="Preview ODO Akhir">
                            </div>
                            <small class="text-muted d-block mt-1">Foto odometer setelah unit selesai beroperasi (opsional, maks 5MB)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted" style="font-size:.85rem;">Catatan (opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan akhir shift..." style="border-radius:10px;font-size:.88rem;">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit-ts mb-3">
                <i class="bi bi-save me-2"></i>Simpan Timesheet
            </button>
        </div>

    </form>
    @endif

    <div class="text-center mb-4">
        <a href="{{ route('operator.landing') }}" style="color:#9ca3af;font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Portal
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
let kmStart = 0;
// Batas wajar km/ritase untuk peringatan
const KM_PER_RITASE_MIN = 1;    // < 1 km/ritase = anomali
const KM_PER_RITASE_MAX = 100;  // > 100 km/ritase = anomali

function triggerFile(id) {
    // Klik input file (bisa ambil foto langsung di mobile)
    document.getElementById(id).click();
}

function previewFoto(input, previewId, labelId) {
    const preview = document.getElementById(previewId);
    const label   = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            label.textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function onP2HChange(radio) {
    kmStart = parseFloat(radio.dataset.km) || 0;
    document.getElementById('infoUnit').textContent     = radio.dataset.unit;
    document.getElementById('infoOperator').textContent = radio.dataset.operator;
    document.getElementById('infoKmStart').textContent  = kmStart.toLocaleString('id-ID') + ' km';

    const kmInput = document.getElementById('kmEndInput');
    kmInput.min   = kmStart;
    document.getElementById('kmEndHint').textContent = 'Minimal: ' + kmStart.toLocaleString('id-ID') + ' km';
    if (parseFloat(kmInput.value) < kmStart) kmInput.value = kmStart;
    updateCalc();

    document.getElementById('p2hInfoBox').style.display = 'block';
    window.scrollTo({ top: document.getElementById('p2hInfoBox').offsetTop - 80, behavior: 'smooth' });
}

function updateCalc() {
    const kmEnd    = parseFloat(document.getElementById('kmEndInput').value) || 0;
    const retase   = parseInt(document.getElementById('retaseInput').value) || 0;
    const traveled = Math.max(0, kmEnd - kmStart);

    document.getElementById('kmTraveledDisp').textContent = traveled.toLocaleString('id-ID') + ' km';

    // Kalkulasi km per ritase
    const kprEl  = document.getElementById('kmPerRitaseDisp');
    const warnEl = document.getElementById('anomaliWarning');
    const okEl   = document.getElementById('anomaliOk');
    const msgEl  = document.getElementById('anomaliMsg');
    const okMsg  = document.getElementById('anomaliOkMsg');

    if (retase > 0 && traveled > 0) {
        const kpr = traveled / retase;
        kprEl.textContent = kpr.toFixed(1) + ' km';

        if (kpr < KM_PER_RITASE_MIN) {
            // Terlalu kecil
            warnEl.style.display = 'block'; okEl.style.display = 'none';
            msgEl.textContent = ` Jarak rata-rata per ritase terlalu kecil (${kpr.toFixed(1)} km/ritase). Mungkin ada kesalahan input.`;
        } else if (kpr > KM_PER_RITASE_MAX) {
            // Terlalu besar
            warnEl.style.display = 'block'; okEl.style.display = 'none';
            msgEl.textContent = ` Jarak rata-rata per ritase sangat besar (${kpr.toFixed(1)} km/ritase). Mohon periksa kembali.`;
        } else {
            warnEl.style.display = 'none'; okEl.style.display = 'block';
            okMsg.textContent = ` (${kpr.toFixed(1)} km/ritase)`;
        }
    } else if (traveled > 5 && retase === 0) {
        kprEl.textContent = '—';
        warnEl.style.display = 'block'; okEl.style.display = 'none';
        msgEl.textContent = ` ODO bergerak ${traveled.toLocaleString('id-ID')} km tetapi ritase = 0. Apakah ritase sudah benar?`;
    } else {
        kprEl.textContent = '—';
        warnEl.style.display = 'none'; okEl.style.display = 'none';
    }

    // Validasi input km_end
    const input = document.getElementById('kmEndInput');
    if (kmEnd < kmStart) {
        input.setCustomValidity('Odometer akhir tidak boleh kurang dari ' + kmStart);
        input.classList.add('is-invalid');
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name=p2h_check_id]:checked');
    if (checked) onP2HChange(checked);
});
</script>
@include('operator.pwa-register')
</body>
</html>
