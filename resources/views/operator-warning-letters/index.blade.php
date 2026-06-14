@extends('layouts.app')
@section('page-title', 'Surat Peringatan Operator')
@section('breadcrumb')<li class="breadcrumb-item active">Surat Peringatan Operator</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-envelope-exclamation me-2 text-danger"></i>Daftar Surat Peringatan</div>
    </div>
    <div class="erp-card-body">
        @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>No. Surat</th>
                        <th>Tanggal</th>
                        <th>Operator</th>
                        <th>Unit</th>
                        <th>Bulan</th>
                        <th class="text-end">Kelebihan Budget</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td><code>{{ $letter->letter_no }}</code></td>
                        <td>{{ $letter->letter_date->format('d M Y') }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $letter->operator->operator_name ?? '-' }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $letter->operator->operator_code ?? '' }}</div>
                        </td>
                        <td>
                            @if($letter->unit)
                            <a href="{{ route('units.show', $letter->unit) }}">{{ $letter->unit->unit_code }}</a>
                            @else -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary" style="border-radius:999px;">{{ $letter->year_month }}</span>
                        </td>
                        <td class="text-end">
                            <span class="badge badge-soft-danger" style="border-radius:999px;">+IDR {{ number_format($letter->excess_amount, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($letter->acknowledged_at)
                            <span class="badge bg-success" style="border-radius:999px;"><i class="bi bi-pen me-1"></i>Ditanda Tangani</span>
                            @else
                            <span class="badge bg-secondary" style="border-radius:999px;">Menunggu TTD</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('operator-warning-letters.show', $letter) }}" class="btn btn-xs btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('print.warning-letter', $letter) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada surat peringatan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $letters->links() }}</div>
    </div>
</div>
@endsection
