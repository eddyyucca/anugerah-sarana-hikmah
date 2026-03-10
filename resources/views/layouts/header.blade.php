<header class="topbar">
    <div class="container-fluid px-3 px-lg-4">
        <div class="topbar-inner d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#mobileSidebar"
                        aria-controls="mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div>
                    <h1 class="page-title mb-0">{{ $pageTitle ?? 'Dashboard' }}</h1>
                </div>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="user-pill">
                    <div class="user-avatar">EA</div>
                    <div>
                        <div class="user-name">Admin Workshop</div>
                        <div class="user-role">System User</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>