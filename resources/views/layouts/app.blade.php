<<<<<<< HEAD
{{-- resources/views/layouts/app.blade.php --}}
=======
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD
    <title>{{ $title ?? 'Workshop ERP' }}</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome (AdminLTE dependency) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- AdminLTE 3 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">

    {{-- App CSS --}}
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="content-wrapper">
        {{-- Page Header --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right mb-0" style="font-size:.76rem;">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @endif
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="content">
            <div class="container-fluid">
                @include('components.alerts')
                @yield('content')
            </div>
        </div>
    </div>

    @include('layouts.footer')

</div>{{-- /.wrapper --}}

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

@stack('scripts')

<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-init Tom Select
    function initTomSelect() {
        document.querySelectorAll('.tom-select:not(.ts-hidden-accessible)').forEach(function (el) {
            if (!el.tomselect) {
                new TomSelect(el, { allowEmptyOption: true, plugins: ['clear_button'] });
            }
        });
    }

    // Initial load
    initTomSelect();

    // Reinitialize when new elements are added (for dynamic rows)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initTomSelect();
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
</script>

</body>
</html>
=======
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
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
