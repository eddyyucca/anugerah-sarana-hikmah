@extends('layouts.app')
@section('page-title', 'Technicians')
@section('breadcrumb')<li class="breadcrumb-item active">Technicians</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Technician List</div>
        <a href="{{ route('technicians.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Add Technician</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Search</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Code</th><th>Name</th><th>Skill</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($technicians as $tech)
                    <tr>
                        <td><strong>{{ $tech->technician_code }}</strong></td>
                        <td>{{ $tech->technician_name }}</td>
                        <td>{{ $tech->skill ?? '-' }}</td>
                        <td>{{ $tech->phone ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $tech->is_active ? 'available' : 'standby'])</td>
                        <td>
                            <a href="{{ route('technicians.show', $tech) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('technicians.edit', $tech) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('technicians.destroy', $tech) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-muted py-4">No technicians found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $technicians->links() }}</div>
    </div>
</div>
@endsection
