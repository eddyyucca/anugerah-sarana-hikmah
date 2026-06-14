@extends('layouts.app')
@section('page-title', $supplierReturn->return_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('supplier-returns.index') }}">Return Supplier</a></li>
    <li class="breadcrumb-item active">{{ $supplierReturn->return_no }}</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Info Card --}}
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-body">
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem;color:#f59e0b;"><i class="bi bi-arrow-return-left"></i></div>
                    <h5 class="fw-bold mb-0">{{ $supplierReturn->return_no }}</h5>
                    <span class="badge bg-{{ $supplierReturn->status_color }} mt-1">{{ $supplierReturn->status_label }}</span>
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Tanggal</td><td>{{ $supplierReturn->return_date->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted">Supplier</td><td><strong>{{ $supplierReturn->supplier->supplier_name }}</strong></td></tr>
                    @if($supplierReturn->goodsReceipt)
                    <tr><td class="text-muted">Ref GR</td><td>{{ $supplierReturn->goodsReceipt->gr_number }}</td></tr>
                    @endif
                    @if($supplierReturn->confirmed_at)
                    <tr><td class="text-muted">Dikonfirmasi</td><td>{{ $supplierReturn->confirmed_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td class="text-muted">Oleh</td><td>{{ $supplierReturn->confirmed_by }}</td></tr>
                    @endif
                    @if($supplierReturn->sent_at)
                    <tr><td class="text-muted">Dikirim</td><td>{{ $supplierReturn->sent_at->format('d/m/Y H:i') }}</td></tr>
                    @endif
                </table>

                @if($supplierReturn->return_reason)
                <div class="alert alert-warning py-2 mt-2">
                    <small><strong>Alasan:</strong> {{ $supplierReturn->return_reason }}</small>
                </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex flex-column gap-2 mt-3">
                    @if($supplierReturn->status === 'draft')
                    <form action="{{ route('supplier-returns.confirm', $supplierReturn) }}" method="POST"
                        onsubmit="return confirm('Konfirmasi return? Stok akan dikurangi secara otomatis.')">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" style="border-radius:10px;">
                            <i class="bi bi-check-lg me-1"></i>Konfirmasi Return
                        </button>
                    </form>
                    @endif
                    @if($supplierReturn->status === 'confirmed')
                    <form action="{{ route('supplier-returns.send', $supplierReturn) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" style="border-radius:10px;">
                            <i class="bi bi-send me-1"></i>Tandai Terkirim
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('print.supplier-return', $supplierReturn) }}" target="_blank" class="btn btn-outline-secondary w-100" style="border-radius:10px;">
                        <i class="bi bi-printer me-1"></i>Print
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success py-2 mt-3">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger py-2 mt-3">{{ session('error') }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Items Card --}}
    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-list-ul me-2"></i>Item yang Dikembalikan</div>
            </div>
            <div class="erp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Sparepart</th><th>Part Number</th><th class="text-end">Qty Return</th><th>Alasan Cacat</th></tr>
                        </thead>
                        <tbody>
                            @forelse($supplierReturn->items as $item)
                            <tr>
                                <td><strong>{{ $item->sparepart->part_name }}</strong></td>
                                <td class="text-muted">{{ $item->sparepart->part_number ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->qty_returned, 2) }} {{ $item->sparepart->uom }}</td>
                                <td>{{ $item->defect_reason }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada item.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
