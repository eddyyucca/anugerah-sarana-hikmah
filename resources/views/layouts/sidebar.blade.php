@php
    $menus = auth()->check() ? auth()->user()->allowedMenus() : [];
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    // Admin sees everything
    $can = function($key) use ($menus, $isAdmin) { return $isAdmin || in_array($key, $menus); };
@endphp
<aside class="sidebar-desktop">
    <div class="sidebar-brand d-flex align-items-center gap-3">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo" >
        <div>
            <div class="sidebar-title">Workshop ERP</div>
            <div class="sidebar-subtitle">Mining Logistics</div>
        </div>
    </div>

    <div class="sidebar-section">
        @if($can('dashboard'))
        <div class="sidebar-section-title">Main Menu</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-grid-1x2-fill"></i></span><span>Dashboard</span>
        </a>
        @endif

        @if($can('units') || $can('spareparts') || $can('suppliers') || $can('technicians'))
        <div class="sidebar-section-title mt-3">Master Data</div>
        @if($can('units'))
        <a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-truck"></i></span><span>Units</span>
        </a>
        @endif
        @if($can('spareparts'))
        <a href="{{ route('spareparts.index') }}" class="sidebar-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-gear"></i></span><span>Spareparts</span>
        </a>
        @endif
        @if($can('suppliers'))
        <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-building"></i></span><span>Suppliers</span>
        </a>
        @endif
        @if($can('technicians'))
        <a href="{{ route('technicians.index') }}" class="sidebar-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-person-badge"></i></span><span>Technicians</span>
        </a>
        @endif
        @endif

        @if($can('purchase-requests') || $can('consumable-pr') || $can('purchase-orders'))
        <div class="sidebar-section-title mt-3">Procurement</div>
        @if($can('purchase-requests'))
        <a href="{{ route('purchase-requests.index') }}" class="sidebar-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-file-earmark-text"></i></span><span>Purchase Request</span>
        </a>
        @endif
        @if($can('consumable-pr'))
        <a href="{{ route('consumable-pr.index') }}" class="sidebar-link {{ request()->routeIs('consumable-pr.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-droplet"></i></span><span>Consumable PR</span>
        </a>
        @endif
        @if($can('purchase-orders'))
        <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-cart-check"></i></span><span>Purchase Order</span>
        </a>
        @endif
        @endif

        @if($can('goods-receipts') || $can('goods-issues') || $can('warehouse-transfer') || $can('stock-opname'))
        <div class="sidebar-section-title mt-3">Warehouse</div>
        @if($can('goods-receipts'))
        <a href="{{ route('goods-receipts.index') }}" class="sidebar-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-in-down"></i></span><span>Goods Receipt</span>
        </a>
        @endif
        @if($can('goods-issues'))
        <a href="{{ route('goods-issues.index') }}" class="sidebar-link {{ request()->routeIs('goods-issues.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-up"></i></span><span>Goods Issue</span>
        </a>
        @endif
        @if($can('warehouse-transfer'))
        <a href="{{ route('warehouse-transfer.index') }}" class="sidebar-link {{ request()->routeIs('warehouse-transfer.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-arrow-left-right"></i></span><span>Transfer</span>
        </a>
        @endif
        @if($can('stock-opname'))
        <a href="{{ route('stock-opname.index') }}" class="sidebar-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-clipboard2-data"></i></span><span>Stock Opname</span>
        </a>
        @endif
        @endif

        @if($can('work-orders') || $can('downtime'))
        <div class="sidebar-section-title mt-3">Maintenance</div>
        @if($can('work-orders'))
        <a href="{{ route('work-orders.index') }}" class="sidebar-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-tools"></i></span><span>Work Orders</span>
        </a>
        @endif
        @if($can('downtime'))
        <a href="{{ route('downtime.index') }}" class="sidebar-link {{ request()->routeIs('downtime.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-speedometer2"></i></span><span>Downtime Analysis</span>
        </a>
        @endif
        @endif

        @if($can('operators') || $can('p2h') || $can('p2h-summary'))
        <div class="sidebar-section-title mt-3">Operation</div>
        @if($can('operators'))
        <a href="{{ route('operators.index') }}" class="sidebar-link {{ request()->routeIs('operators.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-people"></i></span><span>Operators</span>
        </a>
        @endif
        @if($can('p2h'))
        <a href="{{ route('p2h.index') }}" class="sidebar-link {{ request()->routeIs('p2h.index') || request()->routeIs('p2h.create') || request()->routeIs('p2h.show') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-clipboard-check"></i></span><span>P2H Check</span>
        </a>
        @endif
        @if($can('p2h-summary'))
        <a href="{{ route('p2h.summary') }}" class="sidebar-link {{ request()->routeIs('p2h.summary') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-graph-up"></i></span><span>P2H Summary</span>
        </a>
        @endif
        @endif

        @if($can('reports'))
        <div class="sidebar-section-title mt-3">Reports</div>
        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span><span>Reports</span>
        </a>
        @endif

        @if($isAdmin || $can('approval-settings') || $can('menu-settings'))
        <div class="sidebar-section-title mt-3">Settings</div>
        @if($isAdmin || $can('approval-settings'))
        <a href="{{ route('approval-settings.index') }}" class="sidebar-link {{ request()->routeIs('approval-settings.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-sliders"></i></span><span>Approval</span>
        </a>
        @endif
        @if($isAdmin || $can('menu-settings'))
        <a href="{{ route('menu-settings.index') }}" class="sidebar-link {{ request()->routeIs('menu-settings.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-shield-lock"></i></span><span>Menu & Roles</span>
        </a>
        @endif
        @endif
    </div>
</aside>
