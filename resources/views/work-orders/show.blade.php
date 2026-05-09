@extends('layouts.app')
@section('page-title', $workOrder->wo_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">{{ $workOrder->wo_number }}</li>@endsection

@section('content')

{{-- Banner Over Budget --}}
@if($workOrder->status === 'pending_approval')
<div class="alert alert-danger mb-3" style="border-radius:14px;">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-shield-exclamation fs-3 mt-1 flex-shrink-0 text-danger"></i>
        <div>
            <strong class="d-block mb-1">Work Order Menunggu Persetujuan Level Tertinggi</strong>
            <span style="font-size:.9rem;">Unit <strong>{{ $workOrder->unit->unit_code }}</strong> telah melampaui budget perbaikan bulanan.
            WO ini tidak dapat dilanjutkan hingga mendapat persetujuan.</span>
        </div>
    </div>
</div>
@elseif($budgetStatus['has_limit'] && $budgetStatus['is_over_budget'])
<div class="alert alert-warning mb-3" style="border-radius:14px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Perhatian:</strong> Unit <strong>{{ $workOrder->unit->unit_code }}</strong> sedang dalam kondisi over budget bulan ini.
    WO baru untuk unit ini akan membutuhkan persetujuan.
</div>
@endif

<div class="row g-3">
    {{-- Left: Info & Actions --}}
    <div class="col-lg-4">
        <x-card class="p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div style="font-weight:800;font-size:1.2rem;">{{ $workOrder->wo_number }}</div>
                    <div class="text-muted" style="font-size:.85rem;">{{ ucfirst($workOrder->maintenance_type) }}</div>
                </div>
                @include('components.status-badge', ['status' => $workOrder->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Unit</td><td><a href="{{ route('units.show', $workOrder->unit_id) }}">{{ $workOrder->unit->unit_code }}</a> - {{ $workOrder->unit->unit_model }}</td></tr>
                <tr><td class="text-muted">Operator</td><td>
                    @if($workOrder->operator)
                        <a href="{{ route('operators.show', $workOrder->operator) }}">{{ $workOrder->operator->operator_name }}</a>
                        <span class="text-muted" style="font-size:.8rem;">({{ $workOrder->operator->operator_code }})</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td></tr>
                <tr><td class="text-muted">Teknisi</td><td>{{ $workOrder->technician->technician_name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Mulai</td><td>{{ $workOrder->start_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                <tr><td class="text-muted">Selesai</td><td>{{ $workOrder->end_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                <tr><td class="text-muted">Downtime</td><td>{{ $workOrder->downtime_hours }} jam</td></tr>
                <tr><td class="text-muted">Lokasi</td><td>
                    @if($workOrder->repair_location === 'di_luar_workshop')
                        <span class="badge bg-warning text-dark">Di Luar Workshop</span>
                    @else
                        <span class="badge bg-secondary">Di Workshop</span>
                    @endif
                </td></tr>
                @if($workOrder->action_taken)<tr><td class="text-muted">Tindakan</td><td>{{ $workOrder->action_taken }}</td></tr>@endif
                @if($workOrder->remarks)<tr><td class="text-muted">Keterangan</td><td>{{ $workOrder->remarks }}</td></tr>@endif
            </table>

            {{-- Action Buttons --}}
            <div class="d-flex flex-wrap gap-2">
                @if($workOrder->status === 'open')
                    <form action="{{ route('work-orders.progress', $workOrder) }}" method="POST">@csrf
                        <x-button type="submit" variant="warning" size="sm">Mulai Proses</x-button>
                    </form>
                @endif
                @if(in_array($workOrder->status, ['in_progress','waiting_part']))
                    <form action="{{ route('work-orders.complete', $workOrder) }}" method="POST">@csrf
                        <x-button type="submit" variant="success" size="sm" onclick="return confirm('Selesaikan WO ini?')"><i class="bi bi-check-lg me-1"></i>Selesai</x-button>
                    </form>
                @endif
                @if(!in_array($workOrder->status, ['completed','cancelled']))
                    <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endif
                @if($workOrder->status !== 'completed' && $workOrder->status !== 'cancelled' && $workOrder->status !== 'pending_approval')
                    <a href="{{ route('goods-issues.create', ['wo_id' => $workOrder->id]) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-up me-1"></i>Keluarkan Part</a>
                @endif
            </div>

            {{-- Approval Buttons for pending_approval WO --}}
            @if($workOrder->status === 'pending_approval' && $canApprove)
            <hr class="my-3">
            <div class="d-flex flex-column gap-2">
                <div style="font-size:.82rem;font-weight:600;color:#374151;">Tindakan Persetujuan</div>
                <form action="{{ route('work-orders.approve', $workOrder) }}" method="POST">
                    @csrf
                    <input type="text" name="remarks" class="form-control form-control-sm mb-2" placeholder="Catatan persetujuan (opsional)">
                    <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Setujui WO ini?')">
                        <i class="bi bi-check-circle me-1"></i>Setujui WO
                    </button>
                </form>
                <form action="{{ route('work-orders.reject', $workOrder) }}" method="POST">
                    @csrf
                    <input type="text" name="remarks" class="form-control form-control-sm mb-2" placeholder="Alasan penolakan (wajib)" required>
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Tolak dan batalkan WO ini?')">
                        <i class="bi bi-x-circle me-1"></i>Tolak WO
                    </button>
                </form>
            </div>
            @elseif($workOrder->status === 'pending_approval' && !$canApprove)
            <hr class="my-3">
            <div class="p-2 rounded-2 text-center" style="background:#f9fafb;font-size:.82rem;color:#6b7280;">
                <i class="bi bi-lock me-1"></i>Menunggu persetujuan dari approver yang ditentukan.
            </div>
            @endif
        </x-card>

        {{-- Cost Summary --}}
        <x-card class="p-3 mb-3">
            <div class="section-title mb-3">Ringkasan Biaya</div>
            @php
                $spCost    = $workOrder->costSummary->sparepart_cost ?? 0;
                $totalCost = ($spCost) + $workOrder->labor_cost + $workOrder->vendor_cost + $workOrder->consumable_cost;
            @endphp
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Suku Cadang</span><span>IDR {{ number_format($spCost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Tenaga Kerja</span><span>IDR {{ number_format($workOrder->labor_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Vendor</span><span>IDR {{ number_format($workOrder->vendor_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Consumable</span><span>IDR {{ number_format($workOrder->consumable_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box summary-highlight"><div class="d-flex justify-content-between"><strong>Total</strong><strong>IDR {{ number_format($totalCost, 0, ',', '.') }}</strong></div></div>
        </x-card>

        {{-- Budget Status Widget --}}
        @if($budgetStatus['has_limit'])
        <x-card class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="section-title" style="margin-bottom:0;">Budget Unit Bulan Ini</div>
                @if($budgetStatus['is_over_budget'])
                    <span class="badge badge-soft-danger" style="border-radius:999px;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Over Budget</span>
                @else
                    <span class="badge badge-soft-success" style="border-radius:999px;"><i class="bi bi-check-circle me-1"></i>Normal</span>
                @endif
            </div>
            <div class="progress mb-2" style="height:10px;border-radius:999px;">
                <div class="progress-bar {{ $budgetStatus['is_over_budget'] ? 'bg-danger' : ($budgetStatus['percentage'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                     style="width:{{ $budgetStatus['percentage'] }}%;border-radius:999px;"></div>
            </div>
            <div class="row g-0 text-center" style="font-size:.8rem;">
                <div class="col-4">
                    <div class="text-muted">Batas</div>
                    <div style="font-weight:600;">IDR {{ number_format($budgetStatus['limit'], 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted">Terpakai</div>
                    <div style="font-weight:600;color:{{ $budgetStatus['is_over_budget'] ? '#dc2626' : 'inherit' }};">IDR {{ number_format($budgetStatus['used'], 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted">Sisa</div>
                    <div style="font-weight:600;">IDR {{ number_format($budgetStatus['remaining'], 0, ',', '.') }}</div>
                </div>
            </div>
        </x-card>
        @endif
    </div>

    {{-- Right: Approval, GI & Logs --}}
    <div class="col-lg-8">

        {{-- Approval History (tampil jika ada) --}}
        @if($approvalLogs->count())
        <x-card class="mb-3">
            <x-slot:header>
                <div class="section-title"><i class="bi bi-shield-check me-2 text-primary"></i>Riwayat Persetujuan</div>
            </x-slot:header>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Level</th><th>Status</th><th>Oleh</th><th>Waktu</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @foreach($approvalLogs as $log)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $log->level_name }}</span></td>
                            <td>@include('components.status-badge', ['status' => $log->action])</td>
                            <td>{{ $log->actor->name ?? '-' }}</td>
                            <td>{{ $log->acted_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="text-muted" style="font-size:.85rem;">{{ $log->remarks ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
        @endif

        <x-card class="mb-3">
            <x-slot:header>
                <div class="section-title">Pengeluaran Barang Terkait</div>
            </x-slot:header>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead><tr><th>No. GI</th><th>Tanggal</th><th>Status</th><th>Item</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($workOrder->goodsIssues as $gi)
                        <tr>
                            <td><a href="{{ route('goods-issues.show', $gi) }}">{{ $gi->gi_number }}</a></td>
                            <td>{{ $gi->issue_date->format('d M Y') }}</td>
                            <td>@include('components.status-badge', ['status' => $gi->status])</td>
                            <td>{{ $gi->items->count() }}</td>
                            <td>IDR {{ number_format($gi->items->sum('total_price'), 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pengeluaran barang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <x-slot:header>
                <div class="section-title">Log Aktivitas</div>
            </x-slot:header>
            @forelse($workOrder->logs->sortByDesc('activity_time') as $log)
            <div class="activity-item mb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="activity-code">{{ ucfirst(str_replace('_',' ',$log->activity_type)) }}</span>
                        <span class="activity-meta ms-2">{{ $log->creator->name ?? 'System' }}</span>
                    </div>
                    <span class="activity-meta">{{ $log->activity_time->format('d M Y H:i') }}</span>
                </div>
                @if($log->description)<div class="activity-meta mt-1">{{ $log->description }}</div>@endif
            </div>
            @empty
            <div class="text-center text-muted py-3">Belum ada log aktivitas.</div>
            @endforelse
        </x-card>
    </div>
</div>
@endsection
