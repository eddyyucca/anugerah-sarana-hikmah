@extends('layouts.app')
@section('page-title', 'Notifications')
@section('breadcrumb')<li class="breadcrumb-item active">Notifications</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
<<<<<<< HEAD
        <div class="section-title"><i class="bi bi-bell mr-2"></i>Notifications</div>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">@csrf
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check2-all mr-1"></i>Mark All Read</button>
=======
        <div class="section-title"><i class="bi bi-bell me-2"></i>Notifications</div>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">@csrf
            <button class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-check2-all me-1"></i>Mark All Read</button>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
        </form>
    </div>
    <div class="erp-card-body">
        @forelse($notifications as $n)
<<<<<<< HEAD
        <a href="{{ route('notifications.read', $n) }}" class="d-block text-decoration-none py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="padding-left:1rem;padding-right:1rem;{{ !$n->is_read ? 'background:#f8fafc;' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @if(!$n->is_read)
                        <span class="d-inline-block rounded-circle bg-danger mr-2" style="width:8px;height:8px;"></span>
                    @endif
                    <strong style="color:var(--text-main);">{{ $n->title }}</strong>
                    @if($n->message)
                        <div class="text-muted" style="font-size:.82rem;">{{ $n->message }}</div>
                    @endif
=======
        <a href="{{ route('notifications.read', $n) }}" class="d-block text-decoration-none py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-radius:8px;padding-left:1rem;padding-right:1rem;{{ !$n->is_read ? 'background:#f8fafc;' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    @if(!$n->is_read)<span class="d-inline-block rounded-circle bg-danger me-2" style="width:8px;height:8px;"></span>@endif
                    <strong style="color:var(--text-main);">{{ $n->title }}</strong>
                    @if($n->message)<div class="text-muted" style="font-size:.82rem;">{{ $n->message }}</div>@endif
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
                </div>
                <span class="text-muted flex-shrink-0" style="font-size:.75rem;">{{ $n->created_at->diffForHumans() }}</span>
            </div>
        </a>
        @empty
<<<<<<< HEAD
        <div class="text-center text-muted py-4">
            <i class="bi bi-bell-slash" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
            No notifications.
        </div>
=======
        <div class="text-center text-muted py-4"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>No notifications.</div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
        @endforelse
        <div class="mt-3">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
