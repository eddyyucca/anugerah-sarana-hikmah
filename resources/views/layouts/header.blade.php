{{-- resources/views/layouts/header.blade.php --}}
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- Left: toggle + breadcrumb --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link text-muted" style="font-size:.8rem;">
                <i class="bi bi-house mr-1"></i> Home
            </a>
        </li>
    </ul>

    {{-- Right: notifications + user --}}
    <ul class="navbar-nav ml-auto align-items-center">

        @auth
        {{-- Notifications --}}
        @php
            $unreadCount  = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
            $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(10)->get();
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="bi bi-bell" style="font-size:1.05rem;"></i>
                @if($unreadCount > 0)
                <span class="badge badge-danger navbar-badge" style="font-size:.58rem;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-right notif-dropdown">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <strong style="font-size:.85rem;">Notifications</strong>
                    @if($unreadCount > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-link p-0" style="font-size:.72rem;">Mark all read</button>
                    </form>
                    @endif
                </div>

                @forelse($recentNotifs as $notif)
                <a href="{{ route('notifications.read', $notif) }}"
                   class="dropdown-item py-2 {{ !$notif->is_read ? 'bg-light' : '' }}"
                   style="white-space:normal;font-size:.82rem;">
                    <div class="d-flex gap-2 align-items-start">
                        @if(!$notif->is_read)
                        <span class="d-inline-block rounded-circle bg-danger flex-shrink-0 mt-1"
                              style="width:6px;height:6px;min-width:6px;"></span>
                        @endif
                        <div>
                            <div style="font-weight:600;line-height:1.3;">{{ Str::limit($notif->title, 48) }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-3" style="font-size:.82rem;">No notifications</div>
                @endforelse

                <div class="border-top text-center px-3 py-2">
                    <a href="{{ route('notifications.index') }}" style="font-size:.78rem;">View All</a>
                </div>
            </div>
        </li>
        @endauth

        {{-- User Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-toggle="dropdown">
                <div class="user-avatar">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'GU' }}
                </div>
                <span class="d-none d-md-inline" style="font-size:.82rem;font-weight:600;color:#333;">
                    {{ auth()->user()->name ?? 'Guest' }}
                </span>
                <span class="d-none d-md-inline text-muted" style="font-size:.72rem;">
                    {{ auth()->check() ? ucfirst(auth()->user()->role) : '' }}
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right"
                 style="border-radius:12px;border:none;box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:180px;">
                @auth
                <a class="dropdown-item" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell mr-2"></i>Notifications
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right mr-2"></i>Logout
                    </button>
                </form>
                @else
                <a class="dropdown-item" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right mr-2"></i>Login
                </a>
                @endauth
            </div>
        </li>

    </ul>
</nav>