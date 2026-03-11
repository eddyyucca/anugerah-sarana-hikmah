@extends('layouts.app')
@section('page-title', 'Suppliers')
@section('breadcrumb')<li class="breadcrumb-item active">Suppliers</li>@endsection
@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Supplier List</div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Add Supplier</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search code / name..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Search</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Code</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($suppliers as $sup)
                    <tr>
                        <td><strong>{{ $sup->supplier_code }}</strong></td>
                        <td>{{ $sup->supplier_name }}</td>
                        <td>{{ $sup->contact_person ?? '-' }}</td>
                        <td>{{ $sup->phone ?? '-' }}</td>
                        <td>{{ $sup->email ?? '-' }}</td>
                        <td>
                            <a href="{{ route('suppliers.show', $sup) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('suppliers.edit', $sup) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-muted py-4">No suppliers found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
