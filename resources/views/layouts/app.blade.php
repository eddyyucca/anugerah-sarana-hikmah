{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Workshop ERP' }}</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    {{-- App CSS --}}
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">

    @stack('styles')

    {{-- Restore sidebar collapsed state before paint --}}
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed-init');
        }
    </script>
</head>

<body style="background:var(--app-bg);">
<div class="app-shell" id="appShell">

    {{-- Sidebar Desktop (hidden on mobile) --}}
    <aside class="sidebar-desktop" id="desktopSidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo">
            <div>
                <div class="sidebar-title">Workshop ERP</div>
                <div class="sidebar-subtitle">Mining Logistics</div>
            </div>
        </div>
        @include('layouts.sidebar')
    </aside>

    {{-- Sidebar overlay (mobile & collapsed desktop) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Mobile Sidebar Offcanvas --}}
    <div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('assets/images/logo.png') }}" height="28" alt="Logo">
                <div>
                    <div class="sidebar-title" style="font-size:.9rem;">Workshop ERP</div>
                    <div class="sidebar-subtitle">Mining Logistics</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('layouts.sidebar')
        </div>
    </div>

    <div class="main-shell">
        {{-- Header / Topbar --}}
        @include('layouts.header')

        {{-- Main Content --}}
        <main class="content-shell">
            @include('components.alerts')
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>

</div>{{-- /.app-shell --}}

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

@stack('scripts')

<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ─── Tom Select ───────────────────────────────────────────────
    function initTomSelect() {
        document.querySelectorAll('.tom-select:not(.ts-hidden-accessible)').forEach(function (el) {
            if (!el.tomselect) {
                new TomSelect(el, { allowEmptyOption: true, plugins: ['clear_button'] });
            }
        });
    }
    initTomSelect();
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) initTomSelect();
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // ─── Desktop Sidebar Toggle ────────────────────────────────────
    const appShell = document.getElementById('appShell');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

    // Restore state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        appShell.classList.add('sidebar-collapsed');
    }
    document.documentElement.classList.remove('sidebar-collapsed-init');

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function () {
            appShell.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', appShell.classList.contains('sidebar-collapsed'));
        });
    }

    // ─── Sidebar Group Collapsible ─────────────────────────────────
    document.addEventListener('click', function (e) {
        const toggle = e.target.closest('[data-sidebar-toggle]');
        if (!toggle) return;

        const content = toggle.nextElementSibling;
        if (!content || !content.classList.contains('sidebar-collapse-content')) return;

        const isOpen = !content.classList.contains('closed');

        if (isOpen) {
            // Closing
            content.style.maxHeight = content.scrollHeight + 'px';
            requestAnimationFrame(function () {
                content.style.maxHeight = '0';
            });
            toggle.classList.add('collapsed');
            setTimeout(function () { content.classList.add('closed'); }, 300);
        } else {
            // Opening
            content.classList.remove('closed');
            content.style.maxHeight = '0';
            requestAnimationFrame(function () {
                content.style.maxHeight = content.scrollHeight + 'px';
                setTimeout(function () { content.style.maxHeight = ''; }, 300);
            });
            toggle.classList.remove('collapsed');
        }
    });
});
</script>

</body>
</html>