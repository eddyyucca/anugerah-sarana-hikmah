@extends('layouts.app')
@section('page-title', 'Users')
@section('breadcrumb')<li class="breadcrumb-item active">Users</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">User List</div>
        <a href="{{ route('users.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Add Account</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search name / email / department..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Search</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Role</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->department ?? '-' }}</td>
                        <td><span class="badge badge-soft-primary" style="border-radius:999px;">{{ ucfirst($user->role) }}</span></td>
                        <td>@include('components.status-badge', ['status' => $user->is_active ? 'available' : 'standby'])</td>
                        <td><small>{{ $user->created_at->format('d M Y') }}</small></td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $users->links() }}</div>
    </div>
</div>
@endsection
