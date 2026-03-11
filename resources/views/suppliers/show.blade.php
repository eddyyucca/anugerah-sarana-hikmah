@extends('layouts.app')
@section('page-title', 'Supplier Detail')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li><li class="breadcrumb-item active">{{ $supplier->supplier_code }}</li>@endsection
@section('content')
<div class="erp-card p-3">
    <div style="font-weight:800;font-size:1.2rem;" class="mb-2">{{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}</div>
    <table class="table table-sm"><tr><td class="text-muted">Contact</td><td>{{ $supplier->contact_person ?? '-' }}</td></tr><tr><td class="text-muted">Phone</td><td>{{ $supplier->phone ?? '-' }}</td></tr><tr><td class="text-muted">Email</td><td>{{ $supplier->email ?? '-' }}</td></tr><tr><td class="text-muted">Address</td><td>{{ $supplier->address ?? '-' }}</td></tr></table>
    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>
@endsection
