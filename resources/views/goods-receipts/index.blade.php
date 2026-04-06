@extends('layouts.app')
@section('page-title', 'Penerimaan Barang')
@section('breadcrumb')<li class="breadcrumb-item active">Penerimaan Barang</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Daftar Penerimaan Barang</div>
        <a href="{{ route('goods-receipts.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Buat GR</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="No. GR..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Status</option>
                    @foreach(['draft' => 'Draf', 'posted' => 'Diposting', 'cancelled' => 'Dibatalkan'] as $key => $label)
                    <option value="{{ $key }}" {{ request('status')==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>No. GR</th><th>Ref PO</th><th>Tanggal</th><th>Item</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($grs as $gr)
                    <tr>
                        <td><a href="{{ route('goods-receipts.show', $gr) }}"><strong>{{ $gr->gr_number }}</strong></a></td>
                        <td>{{ $gr->purchaseOrder->po_number ?? '-' }}</td>
                        <td>{{ $gr->receipt_date->format('d M Y') }}</td>
                        <td>{{ $gr->items_count }}</td>
                        <td>@include('components.status-badge', ['status' => $gr->status])</td>
                        <td><a href="{{ route('goods-receipts.show', $gr) }}" class="btn btn-sm btn-light" style="border-radius:8px;" title="Lihat"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada penerimaan barang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $grs->links() }}</div>
    </div>
</div>
@endsection
