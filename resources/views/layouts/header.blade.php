<header class="topbar">
    <div class="container-fluid px-3 px-lg-4">
        <div class="topbar-inner d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"><i class="bi bi-list fs-5"></i></button>
                <div>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:.82rem;"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>@hasSection('breadcrumb')@yield('breadcrumb')@endif</ol></nav>
                    <h1 class="page-title mb-0">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                @auth
                <div class="dropdown">
                    <button class="btn btn-light border position-relative" type="button" data-bs-toggle="dropdown" style="border-radius:12px;padding:.45rem .7rem;">
                        <i class="bi bi-bell" style="font-size:1.1rem;"></i>
                        @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                        @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.65rem;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width:360px;max-height:400px;overflow-y:auto;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.12);">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <strong style="font-size:.9rem;">Notifications</strong>
                            @if($unreadCount > 0)<form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-link p-0" style="font-size:.75rem;">Mark all read</button></form>@endif
                        </div>
                        @php $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(10)->get(); @endphp
                        @forelse($recentNotifs as $notif)
                        <a href="{{ route('notifications.read', $notif) }}" class="dropdown-item py-2 {{ !$notif->is_read ? 'bg-light' : '' }}" style="white-space:normal;font-size:.85rem;">
                            <div class="d-flex gap-2">
                                @if(!$notif->is_read)<span class="d-inline-block rounded-circle bg-danger flex-shrink-0 mt-1" style="width:6px;height:6px;"></span>@endif
                                <div>
                                    <div style="font-weight:600;">{{ Str::limit($notif->title, 50) }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                        @empty<div class="text-center text-muted py-3" style="font-size:.85rem;">No notifications</div>@endforelse
                        <div class="border-top px-3 py-2 text-center"><a href="{{ route('notifications.index') }}" style="font-size:.82rem;">View All</a></div>
                    </div>
                </div>
                @endauth
                <div class="dropdown">
                    <div class="user-pill" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                        <div class="user-avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'GU' }}</div>
                        <div>
                            <div class="user-name">{{ auth()->user()->name ?? 'Guest' }}</div>
                            <div class="user-role">{{ auth()->check() ? ucfirst(auth()->user()->role) : '' }}</div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius:14px;">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i>Notifications</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
                        @else<li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>@endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
