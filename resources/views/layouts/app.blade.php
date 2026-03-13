<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Workshop ERP Dashboard' }}</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <div class="d-none d-lg-block">
            @include('layouts.sidebar')
        </div>

        <div class="main-shell">
            @include('layouts.header')

            <main class="content-shell">
                @include('components.alerts')
                @yield('content')
            </main>

            @include('layouts.footer')
        </div>
    </div>

    @include('layouts.sidebar-mobile')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
    // Auto-init Tom-Select on all .tom-select elements
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tom-select').forEach(el => {
            new TomSelect(el, { allowEmptyOption: true, plugins: ['clear_button'] });
        });
    });
    </script>
</body>
</html>
