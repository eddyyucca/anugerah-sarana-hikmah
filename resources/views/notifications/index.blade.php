@extends('layouts.app')
@section('page-title', 'Notifications')
@section('breadcrumb')<li class="breadcrumb-item active">Notifications</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title"><i class="bi bi-bell me-2"></i>Notifications</div>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">@csrf
            <button class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-check2-all me-1"></i>Mark All Read</button>
        </form>
    </div>
    <div class="erp-card-body">
        @forelse($notifications as $n)
        <a href="{{ route('notifications.read', $n) }}" class="d-block text-decoration-none py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-radius:8px;padding-left:1rem;padding-right:1rem;{{ !$n->is_read ? 'background:#f8fafc;' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @if(!$n->is_read)<span class="d-inline-block rounded-circle bg-danger me-2" style="width:8px;height:8px;"></span>@endif
                    <strong style="color:var(--text-main);">{{ $n->title }}</strong>
                    @if($n->message)<div class="text-muted" style="font-size:.82rem;">{{ $n->message }}</div>@endif
                </div>
                <span class="text-muted flex-shrink-0" style="font-size:.75rem;">{{ $n->created_at->diffForHumans() }}</span>
            </div>
        </a>
        @empty
        <div class="text-center text-muted py-4"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>No notifications.</div>
        @endforelse
        <div class="mt-3">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
