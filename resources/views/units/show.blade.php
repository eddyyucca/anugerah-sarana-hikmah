@extends('layouts.app')
@section('page-title', 'Unit Detail')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">{{ $unit->unit_code }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <x-card>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:rgba(220,38,38,.12);color:#dc2626;width:56px;height:56px;font-size:1.4rem;border-radius:16px;"><i class="bi bi-truck"></i></div>
                <div>
                    <div style="font-weight:800;font-size:1.2rem;">{{ $unit->unit_code }}</div>
                    <div class="text-muted" style="font-size:.85rem;">{{ $unit->unit_model }}</div>
                </div>
            </div>
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">Type</td><td>{{ $unit->unit_type ?? '-' }}</td></tr>
                <tr><td class="text-muted">Category</td><td>{{ $unit->category->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Department</td><td>{{ $unit->department ?? '-' }}</td></tr>
                <tr><td class="text-muted">Status</td><td>@include('components.status-badge', ['status' => $unit->current_status])</td></tr>
                <tr><td class="text-muted">Hour Meter</td><td>{{ number_format($unit->hour_meter, 1) }}</td></tr>
                @if($unit->monthly_budget_limit)
                <tr><td class="text-muted">Budget/Bulan</td><td>IDR {{ number_format($unit->monthly_budget_limit, 0, ',', '.') }}</td></tr>
                @endif
            </table>

            {{-- Budget Status Card --}}
            @if($budgetStatus['has_limit'])
            <div class="mt-3 p-3 rounded-3" style="background:{{ $budgetStatus['is_over_budget'] ? 'rgba(220,38,38,.08)' : 'rgba(16,185,129,.08)' }};border:1px solid {{ $budgetStatus['is_over_budget'] ? 'rgba(220,38,38,.2)' : 'rgba(16,185,129,.2)' }};">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:.82rem;font-weight:600;">Budget Bulan Ini ({{ now()->translatedFormat('F Y') }})</span>
                    @if($budgetStatus['is_over_budget'])
                        <span class="badge badge-soft-danger" style="border-radius:999px;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Over Budget</span>
                    @else
                        <span class="badge badge-soft-success" style="border-radius:999px;"><i class="bi bi-check-circle me-1"></i>Normal</span>
                    @endif
                </div>
                <div class="progress mb-1" style="height:8px;border-radius:999px;">
                    <div class="progress-bar {{ $budgetStatus['is_over_budget'] ? 'bg-danger' : ($budgetStatus['percentage'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                         style="width:{{ $budgetStatus['percentage'] }}%;border-radius:999px;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.78rem;color:#6b7280;">
                    <span>Terpakai: <strong>IDR {{ number_format($budgetStatus['used'], 0, ',', '.') }}</strong></span>
                    <span>{{ $budgetStatus['percentage'] }}%</span>
                    <span>Sisa: <strong>IDR {{ number_format($budgetStatus['remaining'], 0, ',', '.') }}</strong></span>
                </div>
                @if($budgetStatus['is_over_budget'])
                <div class="mt-2" style="font-size:.78rem;color:#dc2626;">
                    <i class="bi bi-info-circle me-1"></i>
                    WO baru untuk unit ini membutuhkan persetujuan level tertinggi.
                    @if($budgetStatus['exceeded_at'])
                    Terlampaui sejak: {{ $budgetStatus['exceeded_at']->format('d M Y H:i') }}
                    @endif
                </div>
                @endif
            </div>
            @endif

            {{-- KM Budget Status --}}
            @if($kmBudgetStatus['has_limit'])
            @php $kmSt = $kmBudgetStatus; @endphp
            <div class="mt-2 p-3 rounded-3" style="background:{{ $kmSt['is_over_km_budget'] ? 'rgba(220,38,38,.08)' : 'rgba(59,130,246,.08)' }};border:1px solid {{ $kmSt['is_over_km_budget'] ? 'rgba(220,38,38,.2)' : 'rgba(59,130,246,.2)' }};">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:.82rem;font-weight:600;"><i class="bi bi-speedometer2 me-1 text-primary"></i>Budget KM Bulan Ini</span>
                    @if($kmSt['is_over_km_budget'])
                        <span class="badge bg-danger" style="border-radius:999px;font-size:.72rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Over KM</span>
                    @else
                        <span class="badge bg-primary" style="border-radius:999px;font-size:.72rem;"><i class="bi bi-check-circle me-1"></i>Normal</span>
                    @endif
                </div>
                <div class="progress mb-1" style="height:8px;border-radius:999px;">
                    <div class="progress-bar {{ $kmSt['is_over_km_budget'] ? 'bg-danger' : ($kmSt['percentage'] >= 80 ? 'bg-warning' : 'bg-primary') }}"
                         style="width:{{ $kmSt['percentage'] }}%;border-radius:999px;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.78rem;color:#6b7280;">
                    <span>Tempuh: <strong>{{ number_format($kmSt['used'], 0, ',', '.') }} km</strong></span>
                    <span>{{ $kmSt['percentage'] }}%</span>
                    <span>Sisa: <strong>{{ number_format($kmSt['remaining'], 0, ',', '.') }} km</strong></span>
                </div>
            </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                @if($budgetStatus['has_limit'])
                <a href="{{ route('operator-performance.index', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-outline-warning ms-2"><i class="bi bi-person-exclamation me-1"></i>Performa Operator</a>
                @endif
            </div>
        </x-card>
    </div>
    <div class="col-lg-8">
        <x-card title="Work Order History">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead><tr><th>WO</th><th>Type</th><th>Technician</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($unit->workOrders->take(20) as $wo)
                        <tr>
                            <td><a href="{{ route('work-orders.show', $wo) }}">{{ $wo->wo_number }}</a></td>
                            <td>{{ ucfirst($wo->maintenance_type) }}</td>
                            <td>{{ $wo->technician->technician_name ?? '-' }}</td>
                            <td>@include('components.status-badge', ['status' => $wo->status])</td>
                            <td>{{ $wo->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No work orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Panel Ban Unit --}}
    <div class="col-12">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title">
                    <i class="bi bi-circle me-2 text-danger"></i>
                    Ban — {{ $unit->unit_code }}
                    <small class="text-muted fw-normal ms-1">({{ $unit->wheel_count ?? 8 }} roda | ODO: {{ number_format($unit->current_odometer, 0, ',', '.') }} km)</small>
                </div>
                <a href="{{ route('tires.install-form', $unit) }}" class="btn btn-sm btn-danger" style="border-radius:10px;">
                    <i class="bi bi-plus-lg me-1"></i>Pasang Ban
                </a>
            </div>
            <div class="erp-card-body p-0">
                @php $tiresByPos = $unit->tires->keyBy('position_number'); @endphp
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Posisi</th>
                                <th>Ban (Sparepart)</th>
                                <th>Total KM</th>
                                <th>Batas KM</th>
                                <th>Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unit->wheel_position_labels as $pos => $label)
                            @php $tire = $tiresByPos->get($pos); @endphp
                            <tr>
                                <td><span class="badge bg-secondary">#{{ $pos }}</span> {{ $label }}</td>
                                @if($tire)
                                @php
                                    $pct = $tire->usage_percent;
                                    $bar = $pct >= 100 ? 'danger' : ($pct >= 85 ? 'warning' : ($pct >= 60 ? 'info' : 'success'));
                                @endphp
                                <td>
                                    <a href="{{ route('tires.show', $tire) }}" class="text-decoration-none">
                                        {{ $tire->sparepart->part_name ?? '-' }}
                                    </a>
                                    @if($tire->serial_number)
                                    <br><small class="text-muted"><code>{{ $tire->serial_number }}</code></small>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($tire->current_km, 0, ',', '.') }} km</strong></td>
                                <td class="text-muted">{{ number_format($tire->km_limit, 0, ',', '.') }} km</td>
                                <td style="min-width:130px;">
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:8px;border-radius:4px;">
                                            <div class="progress-bar bg-{{ $bar }}" style="width:{{ min(100,$pct) }}%"></div>
                                        </div>
                                        <small class="text-{{ $bar }}">{{ $pct }}%</small>
                                    </div>
                                    @if($tire->remaining_km <= 2000)
                                    <small class="text-{{ $bar }}">Sisa {{ number_format($tire->remaining_km, 0, ',', '.') }} km</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tires.move-form', $tire) }}" class="btn btn-xs btn-outline-secondary me-1" title="Pindah">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </a>
                                    <button class="btn btn-xs btn-outline-danger" title="Lepas"
                                        onclick="confirmRemove({{ $tire->id }}, '{{ $tire->sparepart->part_name ?? '-' }}', '{{ $label }}')">
                                        <i class="bi bi-dash-circle"></i>
                                    </button>
                                </td>
                                @else
                                <td colspan="4" class="text-muted" style="font-size:.85rem;"><i class="bi bi-dash"></i> Kosong</td>
                                <td>
                                    <a href="{{ route('tires.install-form', $unit) }}?pos={{ $pos }}" class="btn btn-xs btn-outline-success" title="Pasang ban">
                                        <i class="bi bi-plus-circle"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Lepas Ban --}}
<div class="modal fade" id="removeModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="removeForm" method="POST">
                @csrf
                <div class="modal-header py-2">
                    <h6 class="modal-title">Lepas Ban</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="removeDesc" class="small text-muted mb-3"></p>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Tanggal Dilepas</label>
                        <input type="date" name="removed_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Alasan</label>
                        <select name="removed_reason" class="form-select form-select-sm" required>
                            <option value="aus">Aus / habis masa pakai</option>
                            <option value="rotasi">Rotasi ban</option>
                            <option value="rusak">Rusak</option>
                            <option value="servis">Dibawa servis</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="return_to_stock" value="1" id="returnStock">
                        <label class="form-check-label small" for="returnStock">Kembalikan ke stok inventory</label>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger">Lepas</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmRemove(tireId, partName, posLabel) {
    document.getElementById('removeForm').action = `/tires/${tireId}/remove`;
    document.getElementById('removeDesc').textContent = `${partName} — ${posLabel}`;
    new bootstrap.Modal(document.getElementById('removeModal')).show();
}
</script>
@endpush
@endsection
