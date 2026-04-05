@extends('layouts.app')
@section('page-title', $user->name)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li><li class="breadcrumb-item active">{{ $user->name }}</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title">{{ $user->name }}</div>
        <div class="gap-2 d-flex">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="erp-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Name</label>
                    <p>{{ $user->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Department</label>
                    <p>{{ $user->department ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <p><span class="badge badge-soft-primary" style="border-radius:999px;">{{ ucfirst($user->role) }}</span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <p>@include('components.status-badge', ['status' => $user->is_active ? 'available' : 'standby'])</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Created</label>
                    <p>{{ $user->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
