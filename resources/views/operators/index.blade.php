@extends('layouts.app')
@section('page-title', 'Operators')
@section('breadcrumb')<li class="breadcrumb-item active">Operators</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Operator List</div>
        <a href="{{ route('operators.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Add Operator</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search code / name / NIK..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Search</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Code</th><th>Name</th><th>NIK</th><th>Phone</th><th>License</th><th>Expiry</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($operators as $op)
                    <tr>
                        <td><strong>{{ $op->operator_code }}</strong></td>
                        <td>{{ $op->operator_name }}</td>
                        <td>{{ $op->nik ?? '-' }}</td>
                        <td>{{ $op->phone ?? '-' }}</td>
                        <td>{{ $op->license_type ?? '-' }}</td>
                        <td>
                            @if($op->license_expiry)
                                <span class="{{ $op->isLicenseExpired() ? 'text-danger fw-bold' : ($op->isLicenseExpiringSoon() ? 'text-warning fw-bold' : '') }}">
                                    {{ $op->license_expiry->format('d M Y') }}
                                </span>
                                @if($op->isLicenseExpired()) <span class="badge badge-soft-danger" style="border-radius:999px;">Expired</span> @endif
                            @else - @endif
                        </td>
                        <td>@include('components.status-badge', ['status' => $op->is_active ? 'available' : 'standby'])</td>
                        <td>
                            <a href="{{ route('operators.show', $op) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('operators.edit', $op) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('operators.destroy', $op) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="8" class="text-center text-muted py-4">No operators found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $operators->links() }}</div>
    </div>
</div>
@endsection
