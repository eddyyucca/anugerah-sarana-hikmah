@extends('layouts.app')
@section('page-title', 'Performa Operator')
@section('breadcrumb')<li class="breadcrumb-item active">Performa Operator</li>@endsection

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="erp-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title" style="margin:0;"><i class="bi bi-person-exclamation me-2 text-danger"></i>Ringkasan Pelanggaran Budget – {{ now()->translatedFormat('F Y') }}</div>
                <a href="{{ route('operator-performance.index') }}" class="btn btn-sm btn-light" style="border-radius:10px;"><i class="bi bi-arrow-repeat me-1"></i>Refresh</a>
            </div>
            @if($summary->isEmpty())
            <div class="text-center text-muted py-3">
                <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                Tidak ada pelanggaran budget bulan ini.
            </div>
            @else
            <div class="row g-3">
                @foreach($summary as $item)
                <div class="col-md-4 col-lg-3">
                    <div class="p-3 rounded-3" style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.18);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="kpi-icon" style="background:rgba(220,38,38,.15);color:#dc2626;width:38px;height:38px;font-size:1rem;border-radius:10px;flex-shrink:0;"><i class="bi bi-person-fill-exclamation"></i></div>
                            <div>
                                <div style="font-weight:700;font-size:.95rem;">{{ $item->operator->operator_name ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $item->operator->operator_code ?? '' }}</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:.82rem;">
                            <span class="text-muted">Pelanggaran</span>
                            <span class="badge badge-soft-danger" style="border-radius:999px;">{{ $item->total_violations }}x</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.82rem;">
                            <span class="text-muted">Total Kelebihan</span>
                            <span style="font-weight:600;color:#dc2626;">IDR {{ number_format($item->total_excess, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Filter + Table --}}
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-table me-2"></i>Riwayat Pelanggaran Budget Perbaikan</div>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="operator_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Operator</option>
                    @foreach($operators as $op)
                    <option value="{{ $op->id }}" {{ request('operator_id')==$op->id?'selected':'' }}>{{ $op->operator_code }} - {{ $op->operator_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="unit_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">Semua Unit</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }} - {{ $u->unit_model }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="month" name="year_month" class="form-control form-control-sm" value="{{ request('year_month') }}" style="border-radius:10px;">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('operator-performance.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Operator</th>
                        <th>Unit</th>
                        <th>WO</th>
                        <th>Bulan</th>
                        <th class="text-end">Budget Limit</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-end">Kelebihan</th>
                        <th>Tercatat</th>
                        <th>Surat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $rec->operator->operator_name ?? '-' }}</div>
                            <div class="text-muted" style="font-size:.78rem;">{{ $rec->operator->operator_code ?? '' }}</div>
                        </td>
                        <td>
                            @if($rec->unit)
                            <a href="{{ route('units.show', $rec->unit) }}">{{ $rec->unit->unit_code }}</a>
                            <div class="text-muted" style="font-size:.78rem;">{{ $rec->unit->unit_model }}</div>
                            @else -
                            @endif
                        </td>
                        <td>
                            @if($rec->workOrder)
                            <a href="{{ route('work-orders.show', $rec->workOrder) }}">{{ $rec->workOrder->wo_number }}</a>
                            @else -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary" style="border-radius:999px;">{{ $rec->year_month }}</span>
                        </td>
                        <td class="text-end">IDR {{ number_format($rec->monthly_budget_limit, 0, ',', '.') }}</td>
                        <td class="text-end" style="color:#dc2626;font-weight:600;">IDR {{ number_format($rec->total_cost_at_exceedance, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <span class="badge badge-soft-danger" style="border-radius:999px;">+IDR {{ number_format($rec->excess_amount, 0, ',', '.') }}</span>
                        </td>
                        <td style="font-size:.82rem;">{{ $rec->recorded_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            @php $letter = $rec->warningLetter ?? null; @endphp
                            @if($letter)
                            <a href="{{ route('operator-warning-letters.show', $letter) }}" class="btn btn-xs btn-outline-warning">
                                <i class="bi bi-envelope me-1"></i>{{ $letter->letter_no }}
                            </a>
                            @else
                            <button type="button" class="btn btn-xs btn-outline-danger"
                                onclick="showLetterModal({{ $rec->id }}, '{{ addslashes($rec->notes ?? '') }}')">
                                <i class="bi bi-envelope-plus"></i> Buat
                            </button>
                            @endif
                        </td>
                    </tr>
                    @if($rec->notes)
                    <tr class="table-light">
                        <td colspan="9" style="font-size:.8rem;color:#6b7280;padding:.3rem .75rem;">
                            <i class="bi bi-info-circle me-1"></i>{{ $rec->notes }}
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Tidak ada catatan pelanggaran budget.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $records->links() }}</div>
    </div>
</div>

{{-- Modal Buat Surat Peringatan --}}
<div class="modal fade" id="letterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('operator-warning-letters.store') }}" method="POST">
                @csrf
                <input type="hidden" name="operator_performance_record_id" id="modalRecordId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-envelope-plus me-2"></i>Buat Surat Peringatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Pelanggaran / Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="violation_description" class="form-control" rows="4" id="modalDesc" required
                            placeholder="Jelaskan pelanggaran yang dilakukan operator..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-save me-1"></i>Buat Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLetterModal(recordId, defaultDesc) {
    document.getElementById('modalRecordId').value = recordId;
    document.getElementById('modalDesc').value = defaultDesc;
    new bootstrap.Modal(document.getElementById('letterModal')).show();
}
</script>
@endpush
@endsection
