<aside class="sidebar-desktop">
    <div class="sidebar-brand d-flex align-items-center gap-3">
        <div class="sidebar-logo">W</div>
        <div>
            <div class="sidebar-title">Workshop ERP</div>
            <div class="sidebar-subtitle">Mining Logistics</div>
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Main Menu</div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-section-title mt-3">Master Data</div>

        <a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-truck"></i></span>
            <span>Units</span>
        </a>

        <a href="{{ route('spareparts.index') }}" class="sidebar-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-gear"></i></span>
            <span>Spareparts</span>
        </a>

        <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-building"></i></span>
            <span>Suppliers</span>
        </a>

        <a href="{{ route('technicians.index') }}" class="sidebar-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-person-badge"></i></span>
            <span>Technicians</span>
        </a>

        <div class="sidebar-section-title mt-3">Procurement</div>

        <a href="{{ route('purchase-requests.index') }}" class="sidebar-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-file-earmark-text"></i></span>
            <span>Purchase Request</span>
        </a>

        <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-cart-check"></i></span>
            <span>Purchase Order</span>
        </a>

        <div class="sidebar-section-title mt-3">Warehouse</div>

        <a href="{{ route('goods-receipts.index') }}" class="sidebar-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-in-down"></i></span>
            <span>Goods Receipt</span>
        </a>

        <a href="{{ route('goods-issues.index') }}" class="sidebar-link {{ request()->routeIs('goods-issues.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-box-arrow-up"></i></span>
            <span>Goods Issue</span>
        </a>

        <div class="sidebar-section-title mt-3">Maintenance</div>

        <a href="{{ route('work-orders.index') }}" class="sidebar-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-tools"></i></span>
            <span>Work Orders</span>
        </a>

        <div class="sidebar-section-title mt-3">Reports</div>

        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span>
            <span>Reports</span>
        </a>
    </div>
</aside>
