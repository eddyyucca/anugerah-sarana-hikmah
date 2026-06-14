@extends('layouts.app')
@section('page-title', 'Return ke Supplier')
@section('breadcrumb')<li class="breadcrumb-item active">Return Supplier</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-arrow-return-left me-2 text-warning"></i>Daftar Return ke Supplier</div>
        <a href="{{ route('supplier-returns.create') }}" class="btn btn-sm btn-warning" style="border-radius:10px;">
            <i class="bi bi-plus-lg me-1"></i>Buat Return
        </a>
    </div>
    <div class="erp-card-body">
        @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="sent" {{ request('status')=='sent' ? 'selected' : '' }}>Terkirim</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="supplier_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary" style="border-radius:10px;">Filter</button>
                <a href="{{ route('supplier-returns.index') }}" class="btn btn-sm btn-light" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>No. Return</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $r)
                    <tr>
                        <td><a href="{{ route('supplier-returns.show', $r) }}"><strong>{{ $r->return_no }}</strong></a></td>
                        <td>{{ $r->return_date->format('d/m/Y') }}</td>
                        <td>{{ $r->supplier->supplier_name }}</td>
                        <td class="text-muted" style="max-width:200px;">{{ Str::limit($r->return_reason, 60) }}</td>
                        <td><span class="badge bg-{{ $r->status_color }}">{{ $r->status_label }}</span></td>
                        <td>
                            <a href="{{ route('supplier-returns.show', $r) }}" class="btn btn-xs btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada dokumen return.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $returns->links() }}
    </div>
</div>
@endsection
