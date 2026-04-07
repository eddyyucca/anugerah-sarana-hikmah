<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fit to Work - Pemeriksaan Kesiapan Operator</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @include('operator.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --primary: #1d4ed8; --bg: #f0f4ff; }
        body { background: var(--bg); font-family: Inter, "Segoe UI", Arial, sans-serif; min-height: 100vh; }

        .ftw-header {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            color: #fff;
            padding: 1.2rem 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .ftw-logo { height: 38px; width: auto; object-fit: contain; }
        .ftw-container { max-width: 560px; margin: 0 auto; padding: 1rem; }

        .ftw-card {
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(29,78,216,.08);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .ftw-card-header {
            padding: 1rem 1.2rem .5rem;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #1e3a8a;
        }
        .ftw-card-body { padding: .5rem 1.2rem 1.2rem; }

        .question-row {
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 1.2rem;
            margin-bottom: .8rem;
            transition: border-color .2s;
        }
        .question-row.answered { border-color: #93c5fd; background: #eff6ff; }
        .question-text { font-weight: 600; font-size: .95rem; margin-bottom: .8rem; }
        .question-text small { display: block; font-weight: 400; color: #6b7280; font-size: .8rem; margin-top: .2rem; }

        .answer-btns { display: flex; gap: .6rem; }
        .answer-btn {
            flex: 1;
            padding: .7rem;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            text-align: center;
            transition: all .15s;
            position: relative;
        }
        .answer-btn input[type=radio] { position: absolute; opacity: 0; }
        .answer-btn.yes:has(input:checked) { background: #dcfce7; border-color: #22c55e; color: #15803d; }
        .answer-btn.no:has(input:checked)  { background: #fee2e2; border-color: #ef4444; color: #b91c1c; }
        .answer-btn:hover { border-color: #93c5fd; }

        .btn-submit-ftw {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #fff; border: none; border-radius: 14px;
            padding: .9rem 2rem; font-weight: 700; font-size: 1rem;
            width: 100%; box-shadow: 0 6px 20px rgba(29,78,216,.3);
            transition: .2s;
        }
        .btn-submit-ftw:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(29,78,216,.4); color: #fff; }

        .success-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #86efac;
            border-radius: 18px;
            padding: 2rem;
            text-align: center;
        }
        .unfit-card {
            background: linear-gradient(135deg, #fff1f2, #fee2e2);
            border: 2px solid #fca5a5;
            border-radius: 18px;
            padding: 2rem;
            text-align: center;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: .4rem;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .85rem;
            margin-bottom: .3rem;
        }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

<div class="ftw-header">
    <div class="ftw-container p-0">
        <a href="{{ route('operator.landing') }}" class="back-link">
            <i class="bi bi-chevron-left"></i> Kembali ke Portal
        </a>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="ftw-logo">
            <div>
                <div style="font-weight:700;font-size:1.05rem;">Fit to Work</div>
                <div style="font-size:.78rem;color:rgba(255,255,255,.6);">Pemeriksaan Kesiapan Operator</div>
            </div>
        </div>
    </div>
</div>

<div class="ftw-container">

    @if(session('success'))
    @php $isFitResult = session('is_fit', true); @endphp
    @if($isFitResult)
    <div class="success-card mb-3">
        <div style="font-size:3.5rem;color:#16a34a;" class="mb-2"><i class="bi bi-check-circle-fill"></i></div>
        <div style="font-weight:700;font-size:1.3rem;color:#15803d;">FIT TO WORK</div>
        <div class="text-muted mt-1" style="font-size:.88rem;">Anda dinyatakan siap bekerja. Lanjutkan ke Pemeriksaan P2H unit.</div>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <a href="{{ route('p2h.form-operator') }}" class="btn btn-success" style="border-radius:12px;">
                <i class="bi bi-clipboard-check me-1"></i> Isi P2H Sekarang
            </a>
            <a href="{{ route('operator.ftw-form') }}" class="btn btn-outline-success" style="border-radius:12px;">
                Isi FTW Lagi
            </a>
        </div>
    </div>
    @else
    <div class="unfit-card mb-3">
        <div style="font-size:3.5rem;color:#dc2626;" class="mb-2"><i class="bi bi-x-circle-fill"></i></div>
        <div style="font-weight:700;font-size:1.3rem;color:#b91c1c;">TIDAK FIT TO WORK</div>
        <div class="text-muted mt-1" style="font-size:.88rem;">Anda tidak diizinkan bekerja pada shift ini. Hubungi supervisor Anda.</div>
        <a href="{{ route('operator.landing') }}" class="btn btn-outline-danger mt-3" style="border-radius:12px;">
            Kembali ke Portal
        </a>
    </div>
    @endif
    @endif

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:14px;">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('operator.ftw-store') }}" method="POST" id="ftwForm">
        @csrf

        {{-- Identitas --}}
        <div class="ftw-card">
            <div class="ftw-card-header"><i class="bi bi-person-badge"></i> Identitas</div>
            <div class="ftw-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Nama Operator <span class="text-danger">*</span></label>
                        <select name="operator_id" class="form-select tom-select" required style="padding:.7rem;">
                            <option value="">-- Pilih Nama Anda --</option>
                            @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>
                                {{ $op->operator_name }} ({{ $op->operator_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="check_date" class="form-control" value="{{ old('check_date', date('Y-m-d')) }}" required style="padding:.7rem;">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Shift</label>
                        <select name="shift" class="form-select" required style="padding:.7rem;">
                            <option value="day"   {{ old('shift','day') === 'day'   ? 'selected' : '' }}>Shift Pagi</option>
                            <option value="night" {{ old('shift') === 'night' ? 'selected' : '' }}>Shift Malam</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pertanyaan Kesiapan --}}
        <div class="ftw-card">
            <div class="ftw-card-header"><i class="bi bi-clipboard2-pulse"></i> Pernyataan Kesiapan</div>
            <div class="ftw-card-body">

                {{-- Pertanyaan 1 --}}
                <div class="question-row" id="q1row">
                    <div class="question-text">
                        Apakah Anda siap bekerja pada shift ini?
                        <small>Merasa segar, istirahat cukup, dan siap menjalankan tugas</small>
                    </div>
                    <div class="answer-btns">
                        <label class="answer-btn yes">
                            <input type="radio" name="siap_bekerja" value="1" {{ old('siap_bekerja','1') === '1' ? 'checked' : '' }} required onchange="checkAnswers()">
                            <i class="bi bi-hand-thumbs-up me-1"></i> Ya, Siap
                        </label>
                        <label class="answer-btn no">
                            <input type="radio" name="siap_bekerja" value="0" {{ old('siap_bekerja') === '0' ? 'checked' : '' }} onchange="checkAnswers()">
                            <i class="bi bi-hand-thumbs-down me-1"></i> Tidak
                        </label>
                    </div>
                </div>

                {{-- Pertanyaan 2 --}}
                <div class="question-row" id="q2row">
                    <div class="question-text">
                        Apakah kondisi kesehatan Anda baik saat ini?
                        <small>Tidak demam, tidak pusing, tidak sakit, tidak mengonsumsi obat yang memengaruhi konsentrasi</small>
                    </div>
                    <div class="answer-btns">
                        <label class="answer-btn yes">
                            <input type="radio" name="kondisi_sehat" value="1" {{ old('kondisi_sehat','1') === '1' ? 'checked' : '' }} required onchange="checkAnswers()">
                            <i class="bi bi-heart-pulse me-1"></i> Ya, Sehat
                        </label>
                        <label class="answer-btn no">
                            <input type="radio" name="kondisi_sehat" value="0" {{ old('kondisi_sehat') === '0' ? 'checked' : '' }} onchange="checkAnswers()">
                            <i class="bi bi-emoji-frown me-1"></i> Tidak
                        </label>
                    </div>
                </div>

                {{-- Catatan opsional --}}
                <div class="mt-2">
                    <label class="form-label text-muted" style="font-size:.85rem;">Catatan (opsional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan keterangan jika ada..." style="border-radius:10px;font-size:.88rem;">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Preview status --}}
        <div id="statusPreview" class="mb-3" style="display:none;"></div>

        <button type="submit" class="btn-submit-ftw mb-3">
            <i class="bi bi-check-circle me-2"></i>Kirim Fit to Work
        </button>

        <div class="text-center mb-3">
            <a href="{{ route('operator.landing') }}" style="color:#6b7280;font-size:.83rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Portal
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.querySelectorAll('.ts-wrapper').length === 0 &&
        document.querySelectorAll('select.tom-select').forEach(el => new TomSelect(el, { allowEmptyOption: true }));

    function checkAnswers() {
        const siap  = document.querySelector('input[name=siap_bekerja]:checked');
        const sehat = document.querySelector('input[name=kondisi_sehat]:checked');
        const preview = document.getElementById('statusPreview');

        // update row style
        document.querySelectorAll('.question-row').forEach((row, i) => {
            const checked = document.querySelector(`input[name=${i===0?'siap_bekerja':'kondisi_sehat'}]:checked`);
            row.classList.toggle('answered', !!checked);
        });

        if (siap && sehat) {
            const fit = siap.value === '1' && sehat.value === '1';
            preview.style.display = 'block';
            preview.innerHTML = fit
                ? `<div class="alert mb-0" style="background:#dcfce7;border:1px solid #86efac;border-radius:14px;color:#15803d;font-weight:600;text-align:center;">
                    <i class="bi bi-check-circle-fill me-2"></i>Status Anda: <strong>FIT TO WORK</strong> — Siap bekerja
                   </div>`
                : `<div class="alert mb-0" style="background:#fee2e2;border:1px solid #fca5a5;border-radius:14px;color:#b91c1c;font-weight:600;text-align:center;">
                    <i class="bi bi-x-circle-fill me-2"></i>Status Anda: <strong>TIDAK FIT</strong> — Harap lapor ke supervisor
                   </div>`;
        } else {
            preview.style.display = 'none';
        }
    }

    // Init on page load
    checkAnswers();
</script>
@include('operator.pwa-register')
</body>
</html>
