<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Operator - APEX ERP</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @include('operator.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #dc2626; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .portal-header {
            padding: 1.5rem 1rem 1rem;
            text-align: center;
        }
        .portal-logo {
            height: 48px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            margin-bottom: .75rem;
        }
        .portal-title {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: .5px;
        }
        .portal-subtitle {
            color: rgba(255,255,255,.5);
            font-size: .83rem;
        }

        .portal-container {
            max-width: 520px;
            margin: 0 auto;
            padding: 1rem 1rem 2rem;
            flex: 1;
        }

        .menu-card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 1.5rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
            margin-bottom: .9rem;
        }
        .menu-card:hover, .menu-card:focus {
            background: rgba(255,255,255,.12);
            border-color: rgba(255,255,255,.25);
            transform: translateY(-2px);
            text-decoration: none;
        }
        .menu-card:active { transform: translateY(0); }

        .menu-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }
        .menu-icon-ftw  { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; }
        .menu-icon-p2h  { background: linear-gradient(135deg, #15803d, #22c55e); color: #fff; }
        .menu-icon-ts   { background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff; }

        .menu-label {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
        }
        .menu-desc {
            font-size: .8rem;
            color: rgba(255,255,255,.5);
            margin-top: .15rem;
        }
        .menu-arrow {
            margin-left: auto;
            color: rgba(255,255,255,.3);
            font-size: 1.2rem;
        }

        .step-badge {
            font-size: .7rem;
            padding: .2rem .5rem;
            border-radius: 20px;
            font-weight: 600;
            margin-left: .5rem;
            vertical-align: middle;
        }

        .divider-text {
            text-align: center;
            color: rgba(255,255,255,.3);
            font-size: .78rem;
            margin: .4rem 0 .8rem;
            position: relative;
        }
        .divider-text::before, .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 36%;
            height: 1px;
            background: rgba(255,255,255,.1);
        }
        .divider-text::before { left: 0; }
        .divider-text::after  { right: 0; }

        .footer-note {
            text-align: center;
            color: rgba(255,255,255,.25);
            font-size: .75rem;
            padding-bottom: 1.5rem;
        }

        /* Tombol Install PWA */
        #btnInstallPwa {
            display: none;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 14px;
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            padding: .7rem 1.2rem;
            cursor: pointer;
            width: 100%;
            margin-bottom: .8rem;
            transition: background .2s;
        }
        #btnInstallPwa:hover { background: rgba(255,255,255,.18); }
    </style>
</head>
<body>

<div class="portal-header">
    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="portal-logo">
    <div class="portal-title">Portal Operator</div>
    <div class="portal-subtitle">Pilih formulir yang ingin diisi</div>
</div>

<div class="portal-container">

    {{-- Success flash --}}
    @if(session('success'))
    <div class="alert text-center mb-3" style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#86efac;border-radius:14px;">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Tombol Install PWA --}}
    <button id="btnInstallPwa" onclick="installPwa()">
        <i class="bi bi-download"></i> Pasang Aplikasi di Perangkat Ini
    </button>

    {{-- Urutan pengisian --}}
    <div style="color:rgba(255,255,255,.4);font-size:.75rem;text-align:center;margin-bottom:1rem;letter-spacing:.5px;">
        URUTAN PENGISIAN AWAL SHIFT
    </div>

    {{-- 1. Fit to Work --}}
    <a href="{{ route('operator.ftw-form') }}" class="menu-card">
        <div class="menu-icon menu-icon-ftw">
            <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <div>
            <div class="menu-label">
                Fit to Work
                <span class="step-badge" style="background:rgba(29,78,216,.5);color:#93c5fd;">Langkah 1</span>
            </div>
            <div class="menu-desc">Konfirmasi kesiapan & kondisi kesehatan operator sebelum shift</div>
        </div>
        <i class="bi bi-chevron-right menu-arrow"></i>
    </a>

    {{-- 2. P2H --}}
    <a href="{{ route('p2h.form-operator') }}" class="menu-card">
        <div class="menu-icon menu-icon-p2h">
            <i class="bi bi-clipboard-check-fill"></i>
        </div>
        <div>
            <div class="menu-label">
                Pemeriksaan P2H
                <span class="step-badge" style="background:rgba(21,128,61,.5);color:#86efac;">Langkah 2</span>
            </div>
            <div class="menu-desc">Pemeriksaan kondisi unit sebelum dioperasikan</div>
        </div>
        <i class="bi bi-chevron-right menu-arrow"></i>
    </a>

    <div class="divider-text">setelah selesai bekerja</div>

    {{-- 3. Timesheet --}}
    <a href="{{ route('operator.ts-form') }}" class="menu-card">
        <div class="menu-icon menu-icon-ts">
            <i class="bi bi-clock-history"></i>
        </div>
        <div>
            <div class="menu-label">
                Timesheet Akhir Shift
                <span class="step-badge" style="background:rgba(124,58,237,.5);color:#c4b5fd;">Langkah 3</span>
            </div>
            <div class="menu-desc">Input HM akhir dan jumlah retase setelah shift selesai</div>
        </div>
        <i class="bi bi-chevron-right menu-arrow"></i>
    </a>

</div>

<div class="footer-note">APEX Mining ERP &mdash; Digunakan oleh operator yang berwenang</div>

@include('operator.pwa-register')
</body>
</html>
