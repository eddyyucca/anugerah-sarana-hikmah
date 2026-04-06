@extends('layouts.app')
@section('page-title', 'Permintaan Pembelian')
@section('breadcrumb')<li class="breadcrumb-item active">Permintaan Pembelian</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Daftar Permintaan Pembelian</div>
        <a href="{{ route('purchase-requests.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Buat PR</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="No. PR..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Status</option>
                    @foreach(['draft' => 'Draf', 'submitted' => 'Diajukan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'closed' => 'Ditutup'] as $key => $label)<option value="{{ $key }}" {{ request('status')==$key?'selected':'' }}>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button> <a href="{{ route('purchase-requests.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>No. PR</th><th>Tanggal</th><th>Peminta</th><th>Item</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($prs as $pr)
                    <tr>
                        <td><a href="{{ route('purchase-requests.show', $pr) }}"><strong>{{ $pr->pr_number }}</strong></a></td>
                        <td>{{ $pr->request_date->format('d M Y') }}</td>
                        <td>{{ $pr->requester->name ?? '-' }}</td>
                        <td>{{ $pr->items_count }}</td>
                        <td>@include('components.status-badge', ['status' => $pr->status])</td>
                        <td>
                            <a href="{{ route('purchase-requests.show', $pr) }}" class="btn btn-sm btn-light" style="border-radius:8px;" title="Lihat"><i class="bi bi-eye"></i></a>
                            @if($pr->status === 'draft')
                            <a href="{{ route('purchase-requests.edit', $pr) }}" class="btn btn-sm btn-light" style="border-radius:8px;" title="Edit"><i class="bi bi-pencil"></i></a>
                            @endif
                        </td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada permintaan pembelian ditemukan.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $prs->links() }}</div>
    </div>
</div>
@endsection
