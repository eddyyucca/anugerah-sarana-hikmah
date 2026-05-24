@php
    $menus = auth()->check() ? auth()->user()->allowedMenus() : [];
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $can = function($key) use ($menus, $isAdmin) { return $isAdmin || in_array($key, $menus); };

    // Auto-expand section berdasarkan halaman aktif
    $activeSection = '';
    if (request()->routeIs('operators.*') || request()->routeIs('p2h.*') || request()->routeIs('fit-to-work.*') || request()->routeIs('timesheets.*')) $activeSection = 'operasi';
    elseif (request()->routeIs('operasi.*')) $activeSection = 'operasi-log';
    elseif (request()->routeIs('work-orders.*') || request()->routeIs('downtime.*') || request()->routeIs('odometer.*') || request()->routeIs('tires.*') || request()->routeIs('maintenance.*')) $activeSection = 'pemeliharaan';
    elseif (request()->routeIs('purchase-requests.*') || request()->routeIs('consumable-pr.*') || request()->routeIs('purchase-orders.*')) $activeSection = 'pengadaan';
    elseif (request()->routeIs('goods-receipts.*') || request()->routeIs('goods-issues.*') || request()->routeIs('stock-opname.*')) $activeSection = 'gudang';
    elseif (request()->routeIs('units.*') || request()->routeIs('spareparts.*') || request()->routeIs('suppliers.*') || request()->routeIs('technicians.*')) $activeSection = 'master';
    elseif (request()->routeIs('reports.*')) $activeSection = 'laporan';
    elseif (request()->routeIs('approval-settings.*') || request()->routeIs('menu-settings.*') || request()->routeIs('users.*')) $activeSection = 'pengaturan';
@endphp

<div class="sidebar-section">

    {{-- Dashboard --}}
    @if($can('dashboard'))
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="sidebar-link-icon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span class="sidebar-link-text">Dashboard</span>
    </a>
    @endif

    {{-- Operasi --}}
    @if($can('operators') || $can('p2h') || $can('p2h-summary') || $can('fit-to-work') || $can('timesheets'))
    @php $open = $activeSection === 'operasi'; @endphp
    <div class="sidebar-group mt-2">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Operasi</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('operators'))
            <a href="{{ route('operators.index') }}" class="sidebar-link {{ request()->routeIs('operators.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-people"></i></span>
                <span class="sidebar-link-text">Operator</span>
            </a>
            @endif
            @if($can('fit-to-work'))
            <a href="{{ route('fit-to-work.index') }}" class="sidebar-link {{ request()->routeIs('fit-to-work.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-heart-pulse"></i></span>
                <span class="sidebar-link-text">Fit to Work</span>
            </a>
            @endif
            @if($can('p2h'))
            <a href="{{ route('p2h.index') }}" class="sidebar-link {{ request()->routeIs('p2h.index') || request()->routeIs('p2h.create') || request()->routeIs('p2h.show') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-clipboard-check"></i></span>
                <span class="sidebar-link-text">Pemeriksaan P2H</span>
            </a>
            @endif
            @if($can('timesheets'))
            <a href="{{ route('timesheets.index') }}" class="sidebar-link {{ request()->routeIs('timesheets.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-clock-history"></i></span>
                <span class="sidebar-link-text">Timesheet</span>
            </a>
            @endif
            @if($can('p2h-summary'))
            <a href="{{ route('p2h.summary') }}" class="sidebar-link {{ request()->routeIs('p2h.summary') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-graph-up"></i></span>
                <span class="sidebar-link-text">Ringkasan P2H</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Log & Laporan Operasi --}}
    @if($can('operasi-log') || $can('operasi-laporan'))
    @php $open = $activeSection === 'operasi-log'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Log & Laporan Operasi</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('operasi-log'))
            <a href="{{ route('operasi.log') }}" class="sidebar-link {{ request()->routeIs('operasi.log') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-journal-text"></i></span>
                <span class="sidebar-link-text">Log Harian</span>
            </a>
            @endif
            @if($can('operasi-laporan'))
            <a href="{{ route('operasi.laporan') }}" class="sidebar-link {{ request()->routeIs('operasi.laporan') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span>
                <span class="sidebar-link-text">Laporan & Analisa</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Pemeliharaan --}}
    @if($can('work-orders') || $can('downtime') || $can('odometer') || $can('tires') || $can('maintenance'))
    @php
        $open = $activeSection === 'pemeliharaan';
        $odoAlertCount = \App\Services\OdometerService::countAlerts();
    @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Pemeliharaan</span>
            @if($odoAlertCount > 0)
                <span class="badge bg-danger ms-1" style="font-size:0.65rem;">{{ $odoAlertCount }}</span>
            @endif
            <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('work-orders'))
            <a href="{{ route('work-orders.index') }}" class="sidebar-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-tools"></i></span>
                <span class="sidebar-link-text">Perintah Kerja (WO)</span>
            </a>
            @endif
            @if($can('downtime'))
            <a href="{{ route('downtime.index') }}" class="sidebar-link {{ request()->routeIs('downtime.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-graph-down"></i></span>
                <span class="sidebar-link-text">Analisis Downtime</span>
            </a>
            @endif
            <a href="{{ route('odometer.index') }}" class="sidebar-link {{ request()->routeIs('odometer.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-speedometer2"></i></span>
                <span class="sidebar-link-text">Odometer Unit</span>
            </a>
            <a href="{{ route('tires.index') }}" class="sidebar-link {{ request()->routeIs('tires.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-circle"></i></span>
                <span class="sidebar-link-text">Manajemen Ban</span>
            </a>
            <a href="{{ route('maintenance.index') }}" class="sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-wrench-adjustable"></i></span>
                <span class="sidebar-link-text">
                    Maintenance KM
                    @if($odoAlertCount > 0)
                        <span class="badge bg-danger ms-1" style="font-size:0.65rem;">{{ $odoAlertCount }}</span>
                    @endif
                </span>
            </a>
        </div>
    </div>
    @endif

    {{-- Pengadaan --}}
    @if($can('purchase-requests') || $can('consumable-pr') || $can('purchase-orders'))
    @php $open = $activeSection === 'pengadaan'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Pengadaan</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('purchase-requests'))
            <a href="{{ route('purchase-requests.index') }}" class="sidebar-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-file-earmark-text"></i></span>
                <span class="sidebar-link-text">Permintaan Pembelian (PR)</span>
            </a>
            @endif
            @if($can('consumable-pr'))
            <a href="{{ route('consumable-pr.index') }}" class="sidebar-link {{ request()->routeIs('consumable-pr.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-droplet"></i></span>
                <span class="sidebar-link-text">Permintaan Konsumtif</span>
            </a>
            @endif
            @if($can('purchase-orders'))
            <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-cart-check"></i></span>
                <span class="sidebar-link-text">Pesanan Pembelian (PO)</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Gudang --}}
    @if($can('goods-receipts') || $can('goods-issues') || $can('stock-opname'))
    @php $open = $activeSection === 'gudang'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Gudang</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('goods-receipts'))
            <a href="{{ route('goods-receipts.index') }}" class="sidebar-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-box-arrow-in-down"></i></span>
                <span class="sidebar-link-text">Penerimaan Barang (GR)</span>
            </a>
            @endif
            @if($can('goods-issues'))
            <a href="{{ route('goods-issues.index') }}" class="sidebar-link {{ request()->routeIs('goods-issues.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-box-arrow-up"></i></span>
                <span class="sidebar-link-text">Pengeluaran Barang (GI)</span>
            </a>
            @endif
            @if($can('stock-opname'))
            <a href="{{ route('stock-opname.index') }}" class="sidebar-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-clipboard2-data"></i></span>
                <span class="sidebar-link-text">Opname Stok</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Data Master --}}
    @if($can('units') || $can('spareparts') || $can('suppliers') || $can('technicians'))
    @php $open = $activeSection === 'master'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Data Master</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($can('units'))
            <a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-truck"></i></span>
                <span class="sidebar-link-text">Unit</span>
            </a>
            @endif
            @if($can('spareparts'))
            <a href="{{ route('spareparts.index') }}" class="sidebar-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-gear"></i></span>
                <span class="sidebar-link-text">Suku Cadang</span>
            </a>
            @endif
            @if($can('suppliers'))
            <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-building"></i></span>
                <span class="sidebar-link-text">Pemasok</span>
            </a>
            @endif
            @if($can('technicians'))
            <a href="{{ route('technicians.index') }}" class="sidebar-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-person-badge"></i></span>
                <span class="sidebar-link-text">Teknisi</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Laporan --}}
    @if($can('reports'))
    @php $open = $activeSection === 'laporan'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Laporan</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span>
                <span class="sidebar-link-text">Laporan</span>
            </a>
        </div>
    </div>
    @endif

    {{-- Pengaturan --}}
    @if($isAdmin || $can('approval-settings') || $can('menu-settings') || $can('users'))
    @php $open = $activeSection === 'pengaturan'; @endphp
    <div class="sidebar-group">
        <div class="sidebar-group-toggle {{ $open ? '' : 'collapsed' }}" data-sidebar-toggle>
            <span>Pengaturan</span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </div>
        <div class="sidebar-collapse-content {{ $open ? '' : 'closed' }}">
            @if($isAdmin || $can('users'))
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-person-plus"></i></span>
                <span class="sidebar-link-text">Manajemen Akun</span>
            </a>
            @endif
            @if($isAdmin || $can('approval-settings'))
            <a href="{{ route('approval-settings.index') }}" class="sidebar-link {{ request()->routeIs('approval-settings.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-sliders"></i></span>
                <span class="sidebar-link-text">Persetujuan</span>
            </a>
            @endif
            @if($isAdmin || $can('menu-settings'))
            <a href="{{ route('menu-settings.index') }}" class="sidebar-link {{ request()->routeIs('menu-settings.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-shield-lock"></i></span>
                <span class="sidebar-link-text">Menu & Role</span>
            </a>
            @endif
        </div>
    </div>
    @endif

</div>