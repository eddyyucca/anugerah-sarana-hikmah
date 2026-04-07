<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') — APEX</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --code-color: @yield('code-color', '#3b82f6');
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated background blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.12;
            animation: blobFloat 8s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 { width: 500px; height: 500px; background: @yield('blob1', '#3b82f6'); top: -150px; left: -150px; animation-delay: 0s; }
        .blob-2 { width: 400px; height: 400px; background: @yield('blob2', '#8b5cf6'); bottom: -100px; right: -100px; animation-delay: 3s; }
        .blob-3 { width: 300px; height: 300px; background: @yield('blob3', '#06b6d4'); top: 40%; left: 40%; animation-delay: 1.5s; }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -30px) scale(1.05); }
        }

        /* Card */
        .error-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 520px;
            width: 100%;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 60px rgba(0,0,0,.08), 0 0 0 1px var(--border);
            animation: cardIn .5s cubic-bezier(.34,1.56,.64,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Error code */
        .error-code {
            font-size: 7rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, @yield('gradient-start', '#3b82f6'), @yield('gradient-end', '#8b5cf6'));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: .5rem;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { filter: brightness(1); }
            50%       { filter: brightness(1.15); }
        }

        /* Icon */
        .error-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            animation: iconBounce 2s ease-in-out infinite;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }

        /* Text */
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: .5rem;
        }

        .error-desc {
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* Divider */
        .error-divider {
            height: 1px;
            background: var(--border);
            margin: 1.5rem 0;
        }

        /* Buttons */
        .btn-group-error {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary-error {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1.4rem;
            background: linear-gradient(135deg, @yield('gradient-start', '#3b82f6'), @yield('gradient-end', '#8b5cf6'));
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(59,130,246,.35);
        }
        .btn-primary-error:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59,130,246,.45);
            color: #fff;
        }

        .btn-secondary-error {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1.4rem;
            background: transparent;
            color: var(--muted);
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
            border: 1.5px solid var(--border);
            cursor: pointer;
            transition: background .15s, color .15s, transform .15s;
        }
        .btn-secondary-error:hover {
            background: var(--bg);
            color: var(--text);
            transform: translateY(-2px);
        }

        /* Badge */
        .error-badge {
            display: inline-block;
            padding: .3rem .8rem;
            border-radius: 99px;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            background: @yield('badge-bg', 'rgba(59,130,246,.1)');
            color: @yield('badge-color', '#3b82f6');
            margin-bottom: 1rem;
        }

        /* Footer */
        .error-footer {
            margin-top: 2rem;
            font-size: .78rem;
            color: var(--muted);
            z-index: 1;
            position: relative;
        }

        /* Grid decoration dots */
        .dots-grid {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, #94a3b8 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: .25;
            z-index: 0;
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="dots-grid"></div>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="error-card">
    <span class="error-badge">@yield('badge', 'Error')</span>
    <span class="error-icon">@yield('icon', '⚠️')</span>
    <div class="error-code">@yield('code', '???')</div>
    <div class="error-title">@yield('title', 'Something went wrong')</div>
    <div class="error-desc">@yield('description', 'An unexpected error occurred.')</div>

    <div class="error-divider"></div>

    <div class="btn-group-error">
        <a href="{{ url('/') }}" class="btn-primary-error">
            <i class="bi bi-house-fill"></i> Ke Beranda
        </a>
        <button onclick="history.back()" class="btn-secondary-error">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
    </div>

    @hasSection('extra')
    <div class="error-divider"></div>
    @yield('extra')
    @endif
</div>

<p class="error-footer">
    &copy; {{ date('Y') }} <strong>APEX</strong> &mdash; Fluxa Borneo Tech
</p>

</body>
</html>
