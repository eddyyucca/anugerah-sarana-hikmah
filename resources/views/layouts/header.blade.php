<header class="topbar">
    <div class="container-fluid px-3 px-lg-4">
        <div class="topbar-inner d-flex align-items-center justify-content-between gap-2">

            {{-- Kiri: toggle + judul --}}
            <div class="d-flex align-items-center gap-2 gap-lg-3 min-w-0">
                {{-- Mobile: buka offcanvas sidebar --}}
                <button class="topbar-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                {{-- Desktop: collapse sidebar --}}
                <button class="topbar-btn d-none d-lg-flex" id="sidebarToggleBtn" title="Toggle Sidebar">
                    <i class="bi bi-layout-sidebar fs-5"></i>
                </button>

                <div class="min-w-0">
                    {{-- Breadcrumb: hanya tampil di desktop --}}
                    <nav class="d-none d-lg-block" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size:.78rem;">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                            @hasSection('breadcrumb')@yield('breadcrumb')@endif
                        </ol>
                    </nav>
                    <h1 class="page-title mb-0 text-truncate">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>

            {{-- Kanan: notifikasi + user --}}
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                @auth
                {{-- Budget Alert Indicator --}}
                @php
                    $yearMonthNow = now()->format('Y-m');
                    $overBudgetCount = \App\Models\UnitMonthlyCost::where('year_month', $yearMonthNow)
                        ->where(fn($q) => $q->where('is_over_budget', true)->orWhere('is_over_km_budget', true))
                        ->count();
                    // Tires over km_limit
                    $tireCriticalCount = \App\Models\UnitTire::whereNotNull('unit_id')
                        ->where('km_limit', '>', 0)
                        ->whereColumn('total_km', '>=', 'km_limit')
                        ->count();
                    $totalCritical = $overBudgetCount + $tireCriticalCount;
                @endphp
                <a href="{{ route('dashboard') }}#alertWidget"
                   class="topbar-btn position-relative"
                   title="{{ $totalCritical > 0 ? $totalCritical.' item melewati batas budget/limit' : 'Budget & Limit Status' }}"
                   style="text-decoration:none;">
                    <i class="bi bi-speedometer2 fs-5" style="{{ $totalCritical > 0 ? 'color:#ef4444;' : '' }}"></i>
                    @if($totalCritical > 0)
                    <span class="notif-badge" style="background:#ef4444;">{{ $totalCritical > 99 ? '99+' : $totalCritical }}</span>
                    @endif
                </a>

                {{-- Notifikasi --}}
                <div class="dropdown">
                    <button class="topbar-btn position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                        @if($unreadCount > 0)
                        <span class="notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-dropdown">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <strong style="font-size:.88rem;">Notifikasi</strong>
                            @if($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-link p-0 text-muted" style="font-size:.75rem;">Tandai dibaca</button>
                            </form>
                            @endif
                        </div>
                        @php $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(10)->get(); @endphp
                        @forelse($recentNotifs as $notif)
                        <a href="{{ route('notifications.read', $notif) }}" class="dropdown-item py-2 {{ !$notif->is_read ? 'bg-light' : '' }}" style="white-space:normal;font-size:.84rem;">
                            <div class="d-flex gap-2 align-items-start">
                                @if(!$notif->is_read)
                                <span class="d-inline-block rounded-circle bg-danger flex-shrink-0 mt-1" style="width:6px;height:6px;min-width:6px;"></span>
                                @else
                                <span style="width:6px;min-width:6px;display:inline-block;"></span>
                                @endif
                                <div>
                                    <div style="font-weight:600;line-height:1.3;">{{ Str::limit($notif->title, 45) }}</div>
                                    <div class="text-muted" style="font-size:.74rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center text-muted py-3" style="font-size:.84rem;">Tidak ada notifikasi</div>
                        @endforelse
                        <div class="border-top px-3 py-2 text-center">
                            <a href="{{ route('notifications.index') }}" style="font-size:.8rem;">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                {{-- User --}}
                <div class="dropdown">
                    <div class="user-pill" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        {{-- Nama & role: hanya di desktop --}}
                        <div class="d-none d-lg-block">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius:14px;min-width:180px;">
                        <li class="px-3 py-2 d-lg-none border-bottom">
                            <div class="fw-bold" style="font-size:.88rem;">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size:.76rem;">{{ ucfirst(auth()->user()->role) }}</div>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i>Notifikasi</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Masuk</a>
                @endauth
            </div>

        </div>
    </div>
</header>