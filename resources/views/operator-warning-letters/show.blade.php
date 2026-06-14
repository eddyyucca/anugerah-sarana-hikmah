@extends('layouts.app')
@section('page-title', $operatorWarningLetter->letter_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('operator-performance.index') }}">Performa Operator</a></li>
    <li class="breadcrumb-item active">{{ $operatorWarningLetter->letter_no }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card">
            <div class="erp-card-body">
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem;color:#dc3545;"><i class="bi bi-envelope-exclamation"></i></div>
                    <h5 class="fw-bold mb-0">{{ $operatorWarningLetter->letter_no }}</h5>
                    <div class="text-muted" style="font-size:.85rem;">Surat Keputusan/Peringatan</div>
                    @if($operatorWarningLetter->acknowledged_at)
                    <span class="badge bg-success mt-1">Sudah Ditanda Tangani</span>
                    @else
                    <span class="badge bg-secondary mt-1">Belum Ditanda Tangani</span>
                    @endif
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Tanggal Surat</td><td>{{ $operatorWarningLetter->letter_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Operator</td>
                        <td>
                            <strong>{{ $operatorWarningLetter->operator->operator_name }}</strong><br>
                            <small class="text-muted">{{ $operatorWarningLetter->operator->operator_code }}</small>
                        </td>
                    </tr>
                    <tr><td class="text-muted">Unit</td>
                        <td><a href="{{ route('units.show', $operatorWarningLetter->unit) }}">{{ $operatorWarningLetter->unit->unit_code }}</a></td>
                    </tr>
                    <tr><td class="text-muted">WO Pemicu</td>
                        <td>
                            @if($operatorWarningLetter->workOrder)
                            <a href="{{ route('work-orders.show', $operatorWarningLetter->workOrder) }}">
                                {{ $operatorWarningLetter->workOrder->wo_number }}
                            </a>
                            @else -
                            @endif
                        </td>
                    </tr>
                    <tr><td class="text-muted">Bulan</td><td>{{ $operatorWarningLetter->year_month }}</td></tr>
                    <tr><td class="text-muted">Batas Budget</td><td>IDR {{ number_format($operatorWarningLetter->budget_limit, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Total Biaya</td><td class="text-danger fw-bold">IDR {{ number_format($operatorWarningLetter->total_cost, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Kelebihan</td>
                        <td><span class="badge bg-danger">+IDR {{ number_format($operatorWarningLetter->excess_amount, 0, ',', '.') }}</span></td>
                    </tr>
                    <tr><td class="text-muted">Dibuat oleh</td><td>{{ $operatorWarningLetter->created_by }}</td></tr>
                    @if($operatorWarningLetter->acknowledged_at)
                    <tr><td class="text-muted">Tanda Tangan</td><td>{{ $operatorWarningLetter->acknowledged_at->format('d M Y H:i') }}</td></tr>
                    @endif
                </table>

                <div class="d-flex flex-column gap-2 mt-3">
                    @if(!$operatorWarningLetter->acknowledged_at)
                    <form action="{{ route('operator-warning-letters.acknowledge', $operatorWarningLetter) }}" method="POST"
                        onsubmit="return confirm('Konfirmasi bahwa operator sudah menandatangani surat ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" style="border-radius:10px;">
                            <i class="bi bi-pen me-1"></i>Konfirmasi Tanda Tangan
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('print.warning-letter', $operatorWarningLetter) }}" target="_blank"
                       class="btn btn-outline-secondary w-100" style="border-radius:10px;">
                        <i class="bi bi-printer me-1"></i>Print Surat
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

    <div class="col-lg-8">
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="section-title"><i class="bi bi-card-text me-2"></i>Isi Surat Peringatan</div>
            </div>
            <div class="erp-card-body">
                <div class="p-3 bg-light rounded-3">
                    {{ $operatorWarningLetter->violation_description }}
                </div>

                <div class="mt-4 p-3" style="border:1px solid #e5e7eb;border-radius:10px;">
                    <div class="text-muted small mb-2">Preview Surat</div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td style="font-size:.85rem;">
                                Kepada Yth,<br>
                                <strong>{{ $operatorWarningLetter->operator->operator_name }}</strong><br>
                                ({{ $operatorWarningLetter->operator->operator_code }})<br>
                                Di tempat
                            </td>
                        </tr>
                        <tr><td style="font-size:.85rem;padding-top:.75rem;">
                            Dengan hormat,<br><br>
                            Berdasarkan data perbaikan unit <strong>{{ $operatorWarningLetter->unit->unit_code }}</strong>
                            pada bulan <strong>{{ $operatorWarningLetter->year_month }}</strong>, kami memberikan peringatan
                            karena biaya perbaikan melebihi budget yang ditetapkan.<br><br>
                            Budget perbaikan: <strong>IDR {{ number_format($operatorWarningLetter->budget_limit, 0, ',', '.') }}</strong><br>
                            Total biaya aktual: <strong>IDR {{ number_format($operatorWarningLetter->total_cost, 0, ',', '.') }}</strong><br>
                            Kelebihan: <strong>IDR {{ number_format($operatorWarningLetter->excess_amount, 0, ',', '.') }}</strong><br><br>
                            Demikian surat peringatan ini dibuat untuk diperhatikan.
                        </td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
