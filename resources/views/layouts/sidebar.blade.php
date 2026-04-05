{{-- resources/views/layouts/sidebar.blade.php --}}
@php
    $menus   = auth()->check() ? auth()->user()->allowedMenus() : [];
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $can     = function($key) use ($menus, $isAdmin) { return $isAdmin || in_array($key, $menus); };
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-0">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
             class="brand-image img-circle elevation-0"
             style="opacity:.9;width:32px;height:32px;object-fit:cover;">
        <span class="brand-text font-weight-bold text-white">Workshop ERP</span>
    </a>

    <div class="sidebar">

        {{-- User Panel --}}
        <div class="user-panel mt-3 pb-3 mb-2 d-flex align-items-center">
            <div class="image">
                <div style="width:34px;height:34px;border-radius:50%;background:#c0392b;color:#fff;
                            display:flex;align-items:center;justify-content:center;
                            font-size:.72rem;font-weight:700;">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'GU' }}
                </div>
            </div>
            <div class="info" style="padding-left:.6rem;">
                <a href="#" class="d-block" style="color:rgba(255,255,255,.85);font-size:.82rem;font-weight:600;line-height:1.2;">
                    {{ auth()->user()->name ?? 'Guest' }}
                </a>
                <span style="font-size:.7rem;color:rgba(255,255,255,.4);">
                    {{ auth()->check() ? ucfirst(auth()->user()->role) : '' }}
                </span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="mt-1">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">

                {{-- MAIN --}}
                @if($can('dashboard'))
                <li class="nav-header">MAIN</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-grid-1x2-fill"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @endif

                {{-- MASTER DATA --}}
                @if($can('units') || $can('spareparts') || $can('suppliers') || $can('technicians') || $can('users'))
                <li class="nav-header">MASTER DATA</li>

                @if($can('units'))
                <li class="nav-item">
                    <a href="{{ route('units.index') }}"
                       class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-truck"></i><p>Units</p>
                    </a>
                </li>
                @endif

                @if($can('spareparts'))
                <li class="nav-item">
                    <a href="{{ route('spareparts.index') }}"
                       class="nav-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear"></i><p>Spareparts</p>
                    </a>
                </li>
                @endif

                @if($can('suppliers'))
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}"
                       class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-building"></i><p>Suppliers</p>
                    </a>
                </li>
                @endif

                @if($can('technicians'))
                <li class="nav-item">
                    <a href="{{ route('technicians.index') }}"
                       class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge"></i><p>Technicians</p>
                    </a>
                </li>
                @endif

                @if($can('users'))
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people-fill"></i><p>Users</p>
                    </a>
                </li>
                @endif
                @endif

                {{-- PROCUREMENT --}}
                @if($can('purchase-requests') || $can('consumable-pr') || $can('purchase-orders'))
                <li class="nav-header">PROCUREMENT</li>

                @if($can('purchase-requests'))
                <li class="nav-item">
                    <a href="{{ route('purchase-requests.index') }}"
                       class="nav-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-text"></i><p>Purchase Request</p>
                    </a>
                </li>
                @endif

                @if($can('consumable-pr'))
                <li class="nav-item">
                    <a href="{{ route('consumable-pr.index') }}"
                       class="nav-link {{ request()->routeIs('consumable-pr.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-droplet"></i><p>Consumable PR</p>
                    </a>
                </li>
                @endif

                @if($can('purchase-orders'))
                <li class="nav-item">
                    <a href="{{ route('purchase-orders.index') }}"
                       class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-cart-check"></i><p>Purchase Order</p>
                    </a>
                </li>
                @endif
                @endif

                {{-- WAREHOUSE --}}
                @if($can('goods-receipts') || $can('goods-issues') || $can('warehouse-transfer') || $can('stock-opname'))
                <li class="nav-header">WAREHOUSE</li>

                @if($can('goods-receipts'))
                <li class="nav-item">
                    <a href="{{ route('goods-receipts.index') }}"
                       class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-box-arrow-in-down"></i><p>Goods Receipt</p>
                    </a>
                </li>
                @endif

                @if($can('goods-issues'))
                <li class="nav-item">
                    <a href="{{ route('goods-issues.index') }}"
                       class="nav-link {{ request()->routeIs('goods-issues.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-box-arrow-up"></i><p>Goods Issue</p>
                    </a>
                </li>
                @endif

                @if($can('warehouse-transfer'))
                <li class="nav-item">
                    <a href="{{ route('warehouse-transfer.index') }}"
                       class="nav-link {{ request()->routeIs('warehouse-transfer.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-arrow-left-right"></i><p>Transfer</p>
                    </a>
                </li>
                @endif

                @if($can('stock-opname'))
                <li class="nav-item">
                    <a href="{{ route('stock-opname.index') }}"
                       class="nav-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clipboard2-data"></i><p>Stock Opname</p>
                    </a>
                </li>
                @endif
                @endif

                {{-- MAINTENANCE --}}
                @if($can('work-orders') || $can('downtime'))
                <li class="nav-header">MAINTENANCE</li>

                @if($can('work-orders'))
                <li class="nav-item">
                    <a href="{{ route('work-orders.index') }}"
                       class="nav-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-tools"></i><p>Work Orders</p>
                    </a>
                </li>
                @endif

                @if($can('downtime'))
                <li class="nav-item">
                    <a href="{{ route('downtime.index') }}"
                       class="nav-link {{ request()->routeIs('downtime.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i><p>Downtime Analysis</p>
                    </a>
                </li>
                @endif
                @endif

                {{-- OPERATION --}}
                @if($can('operators') || $can('p2h') || $can('p2h-summary'))
                <li class="nav-header">OPERATION</li>

                @if($can('operators'))
                <li class="nav-item">
                    <a href="{{ route('operators.index') }}"
                       class="nav-link {{ request()->routeIs('operators.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i><p>Operators</p>
                    </a>
                </li>
                @endif

                @if($can('p2h'))
                <li class="nav-item">
                    <a href="{{ route('p2h.index') }}"
                       class="nav-link {{ request()->routeIs('p2h.index') || request()->routeIs('p2h.create') || request()->routeIs('p2h.show') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clipboard-check"></i><p>P2H Check</p>
                    </a>
                </li>
                @endif

                @if($can('p2h-summary'))
                <li class="nav-item">
                    <a href="{{ route('p2h.summary') }}"
                       class="nav-link {{ request()->routeIs('p2h.summary') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-graph-up"></i><p>P2H Summary</p>
                    </a>
                </li>
                @endif
                @endif

                {{-- REPORTS --}}
                @if($can('reports'))
                <li class="nav-header">REPORTS</li>
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}"
                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bar-chart-line"></i><p>Reports</p>
                    </a>
                </li>
                @endif

                {{-- SETTINGS --}}
                @if($isAdmin || $can('approval-settings') || $can('menu-settings'))
                <li class="nav-header">SETTINGS</li>

                @if($isAdmin || $can('approval-settings'))
                <li class="nav-item">
                    <a href="{{ route('approval-settings.index') }}"
                       class="nav-link {{ request()->routeIs('approval-settings.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-sliders"></i><p>Approval</p>
                    </a>
                </li>
                @endif

                @if($isAdmin || $can('menu-settings'))
                <li class="nav-item">
                    <a href="{{ route('menu-settings.index') }}"
                       class="nav-link {{ request()->routeIs('menu-settings.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-lock"></i><p>Menu & Roles</p>
                    </a>
                </li>
                @endif
                @endif

            </ul>
        </nav>
    </div>
</aside>