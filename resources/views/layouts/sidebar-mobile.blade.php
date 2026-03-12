<div class="offcanvas offcanvas-start mobile-offcanvas" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo-img">
            <div>
                <div class="sidebar-title text-white">Workshop ERP</div>
                <div class="sidebar-subtitle">Mining Logistics</div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-grid-1x2-fill"></i></span><span>Dashboard</span>
        </a>
        <a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-truck"></i></span><span>Units</span>
        </a>
        <a href="{{ route('spareparts.index') }}" class="sidebar-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-gear"></i></span><span>Spareparts</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-building"></i></span><span>Suppliers</span>
        </a>
        <a href="{{ route('technicians.index') }}" class="sidebar-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-person-badge"></i></span><span>Technicians</span>
        </a>
        <a href="{{ route('purchase-requests.index') }}" class="sidebar-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-file-earmark-text"></i></span><span>Purchase Request</span>
        </a>
        <a href="{{ route('consumable-pr.index') }}" class="sidebar-link {{ request()->routeIs('consumable-pr.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-droplet"></i></span><span>Consumable PR</span>
        </a>
        <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-cart-check"></i></span><span>Purchase Order</span>
        </a>
        <a href="{{ route('goods-receipts.index') }}" class="sidebar-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-in-down"></i></span><span>Goods Receipt</span>
        </a>
        <a href="{{ route('goods-issues.index') }}" class="sidebar-link {{ request()->routeIs('goods-issues.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-up"></i></span><span>Goods Issue</span>
        </a>
        <a href="{{ route('warehouse-transfer.index') }}" class="sidebar-link {{ request()->routeIs('warehouse-transfer.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-arrow-left-right"></i></span><span>Transfer</span>
        </a>
        <a href="{{ route('stock-opname.index') }}" class="sidebar-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-clipboard2-data"></i></span><span>Stock Opname</span>
        </a>
        <a href="{{ route('work-orders.index') }}" class="sidebar-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-tools"></i></span><span>Work Orders</span>
        </a>
        <a href="{{ route('downtime.index') }}" class="sidebar-link {{ request()->routeIs('downtime.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-speedometer2"></i></span><span>Downtime Analysis</span>
        </a>
        <a href="{{ route('operators.index') }}" class="sidebar-link {{ request()->routeIs('operators.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-people"></i></span><span>Operators</span>
        </a>
        <a href="{{ route('p2h.index') }}" class="sidebar-link {{ request()->routeIs('p2h.index') || request()->routeIs('p2h.create') || request()->routeIs('p2h.show') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-clipboard-check"></i></span><span>P2H Check</span>
        </a>
        <a href="{{ route('p2h.summary') }}" class="sidebar-link {{ request()->routeIs('p2h.summary') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-graph-up"></i></span><span>P2H Summary</span>
        </a>
        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span><span>Reports</span>
        </a>
        <a href="{{ route('approval-settings.index') }}" class="sidebar-link {{ request()->routeIs('approval-settings.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-sliders"></i></span><span>Approval Settings</span>
        </a>
    </div>
</div>
