@extends('layouts.app')
@section('page-title', $stockOpname->opname_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}">Stock Opname</a></li><li class="breadcrumb-item active">{{ $stockOpname->opname_number }}</li>@endsection

@section('content')
<div class="row g-3">

    {{-- LEFT: Header & Info --}}
    <div class="col-lg-4">

        <x-card class="mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div style="font-weight:800;font-size:1.1rem;">{{ $stockOpname->opname_number }}</div>
                    <div class="text-muted" style="font-size:.82rem;">{{ $stockOpname->opname_date->format('d M Y') }}</div>
                </div>
                @php
                    $badgeMap = ['in_progress'=>'warning','pending_approval'=>'info','completed'=>'success','rejected'=>'danger'];
                    $labelMap = ['in_progress'=>'In Progress','pending_approval'=>'Menunggu Approval','completed'=>'Selesai','rejected'=>'Ditolak'];
                @endphp
                <span class="badge bg-{{ $badgeMap[$stockOpname->status] ?? 'secondary' }}">
                    {{ $labelMap[$stockOpname->status] ?? $stockOpname->status }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1" style="font-size:.82rem;">
                    <span class="text-muted">Progress Penghitungan</span>
                    <span><strong>{{ $countedItems }}</strong> / {{ $totalItems }}</span>
                </div>
                <div class="progress" style="height:8px;border-radius:4px;">
                    @php $pct = $totalItems > 0 ? round(($countedItems/$totalItems)*100) : 0; @endphp
                    <div class="progress-bar bg-danger" style="width:{{ $pct }}%"></div>
                </div>
                <div class="text-muted mt-1" style="font-size:.75rem;">{{ $pct }}% selesai</div>
            </div>

            <table class="table table-sm mb-0">
                <tr><td class="text-muted">Dilakukan oleh</td><td>{{ $stockOpname->conductor->name ?? '-' }}</td></tr>
                @if($stockOpname->submitter)
                <tr><td class="text-muted">Diajukan oleh</td><td>{{ $stockOpname->submitter->name }}<br><small class="text-muted">{{ $stockOpname->submitted_at?->format('d M Y H:i') }}</small></td></tr>
                @endif
                @if($stockOpname->approver)
                <tr><td class="text-muted">Disetujui oleh</td><td>{{ $stockOpname->approver->name }}<br><small class="text-muted">{{ $stockOpname->approved_at?->format('d M Y H:i') }}</small></td></tr>
                @endif
                @if($stockOpname->remarks)
                <tr><td class="text-muted">Keterangan</td><td>{{ $stockOpname->remarks }}</td></tr>
                @endif
            </table>
        </x-card>

        {{-- Discrepancy Summary --}}
        @if($countedItems > 0)
        <x-card class="mb-3">
            <div class="section-title mb-2">Ringkasan Selisih</div>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div style="background:#f8f9fa;border-radius:8px;padding:.75rem;">
                        <div style="font-size:1.3rem;font-weight:800;">{{ $discrepancies }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Ada Selisih</div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="background:#fff3cd;border-radius:8px;padding:.75rem;">
                        <div style="font-size:1.3rem;font-weight:800;color:#856404;">{{ $shortItems }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Kurang</div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="background:#d1e7dd;border-radius:8px;padding:.75rem;">
                        <div style="font-size:1.3rem;font-weight:800;color:#0a3622;">{{ $overItems }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Lebih</div>
                    </div>
                </div>
            </div>
        </x-card>
        @endif

        {{-- Submit Button --}}
        @if($stockOpname->status === 'in_progress')
        <x-card class="mb-3">
            @if($countedItems === $totalItems && $totalItems > 0)
                <div class="alert alert-success py-2 mb-2" style="font-size:.83rem;">
                    <i class="bi bi-check-circle me-1"></i> Semua item sudah dihitung!
                </div>
                <form action="{{ route('stock-opname.submit', $stockOpname) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Ajukan opname untuk approval?')">
                        <i class="bi bi-send me-1"></i> Ajukan untuk Approval
                    </button>
                </form>
            @else
                <div class="text-muted" style="font-size:.83rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Sisa <strong>{{ $totalItems - $countedItems }}</strong> item belum dihitung.
                </div>
            @endif
        </x-card>
        @endif

        {{-- Approval Actions --}}
        @if($stockOpname->status === 'pending_approval')
        <x-card class="mb-3">
            <div class="section-title mb-2">Persetujuan Adjustment</div>
            @if($canApprove)
                <form action="{{ route('stock-opname.approve', $stockOpname) }}" method="POST" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Setujui dan sesuaikan stok?')">
                        <i class="bi bi-check-lg me-1"></i> Setujui & Sesuaikan Stok
                    </button>
                </form>
                <form action="{{ route('stock-opname.reject', $stockOpname) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Alasan penolakan..." required>
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Tolak opname ini?')">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                </form>
            @else
                <div class="alert alert-warning py-2 mb-0" style="font-size:.83rem;">
                    <i class="bi bi-lock me-1"></i> Menunggu persetujuan dari approver.
                </div>
            @endif
        </x-card>
        @endif

        {{-- Approval Log --}}
        @if($approvalLogs->count())
        <x-card>
            <div class="section-title mb-2">Log Persetujuan</div>
            @foreach($approvalLogs as $log)
            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <div style="font-size:.82rem;font-weight:600;">{{ $log->level_name }}</div>
                    <div style="font-size:.75rem;" class="text-muted">{{ $log->actor->name ?? '-' }}</div>
                    @if($log->remarks)<div style="font-size:.75rem;" class="text-muted fst-italic">"{{ $log->remarks }}"</div>@endif
                </div>
                <span class="badge bg-{{ match($log->action){ 'approved'=>'success','rejected'=>'danger',default=>'secondary' } }} ms-2">
                    {{ ucfirst($log->action) }}
                </span>
            </div>
            @endforeach
        </x-card>
        @endif

    </div>

    {{-- RIGHT: Counting Table --}}
    <div class="col-lg-8">

        @if($stockOpname->status === 'in_progress')
        <div class="d-flex gap-2 mb-3 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="filterAll" onclick="filterItems('all')">
                Semua <span class="badge bg-secondary ms-1">{{ $totalItems }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" id="filterUncounted" onclick="filterItems('uncounted')">
                Belum Dihitung <span class="badge bg-warning text-dark ms-1">{{ $totalItems - $countedItems }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="filterDisc" onclick="filterItems('discrepancy')">
                Ada Selisih <span class="badge bg-danger ms-1">{{ $discrepancies }}</span>
            </button>
        </div>

        <form action="{{ route('stock-opname.count', $stockOpname) }}" method="POST" id="countForm">
            @csrf
            <x-card>
                <x-slot:header>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="section-title">Penghitungan Item</div>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-floppy me-1"></i> Simpan Progres
                        </button>
                    </div>
                </x-slot:header>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="opnameTable">
                        <thead style="font-size:.78rem;">
                            <tr>
                                <th style="width:70px;">BinLoc</th>
                                <th style="width:100px;">Part No.</th>
                                <th>Nama Part</th>
                                <th style="width:50px;">UOM</th>
                                <th style="width:70px;" class="text-center">Sistem</th>
                                <th style="width:90px;" class="text-center">Fisik</th>
                                <th style="width:70px;" class="text-center">Selisih</th>
                                <th>Catatan</th>
                                <th style="width:50px;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            @php
                                $rowClass = '';
                                $dataFilter = 'all';
                                if ($item->is_counted) {
                                    if ($item->difference < 0) { $rowClass = 'table-danger'; $dataFilter = 'discrepancy'; }
                                    elseif ($item->difference > 0) { $rowClass = 'table-success'; $dataFilter = 'discrepancy'; }
                                } else {
                                    $dataFilter = 'uncounted';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}" data-filter="{{ $dataFilter }}">
                                <td>
                                    <span class="badge bg-dark" style="font-size:.7rem;letter-spacing:.5px;">
                                        {{ $item->sparepart->bin_location ?: '-' }}
                                    </span>
                                </td>
                                <td style="font-size:.78rem;font-weight:600;">{{ $item->sparepart->part_number }}</td>
                                <td style="font-size:.8rem;">{{ $item->sparepart->part_name }}</td>
                                <td style="font-size:.75rem;" class="text-muted">{{ $item->sparepart->uom }}</td>
                                <td class="text-center" style="font-weight:600;">{{ $item->system_qty }}</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="items[{{ $item->id }}][physical_qty]"
                                        class="form-control form-control-sm text-center physical-qty"
                                        value="{{ $item->is_counted ? $item->physical_qty : '' }}"
                                        data-system="{{ $item->system_qty }}"
                                        data-row="{{ $item->id }}"
                                        min="0"
                                        placeholder="{{ $item->is_counted ? $item->physical_qty : '?' }}"
                                        style="min-width:60px;border-radius:6px;">
                                </td>
                                <td class="text-center diff-cell" id="diff-{{ $item->id }}">
                                    @if($item->is_counted)
                                        <strong class="{{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="text"
                                        name="items[{{ $item->id }}][notes]"
                                        class="form-control form-control-sm"
                                        value="{{ $item->notes }}"
                                        placeholder="catatan..."
                                        style="border-radius:6px;font-size:.75rem;">
                                </td>
                                <td class="text-center">
                                    @if($item->is_counted)
                                        <i class="bi bi-check-circle-fill text-success"
                                            title="Dihitung: {{ $item->counter->name ?? '-' }}, {{ $item->counted_at?->format('d/m H:i') }}"></i>
                                    @else
                                        <i class="bi bi-circle text-muted"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted" style="font-size:.82rem;">
                        Tampil: <span id="visibleCount">{{ $totalItems }}</span> item
                    </span>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-floppy me-1"></i> Simpan Progres
                    </button>
                </div>
            </x-card>
        </form>

        @else
        {{-- Read-only view --}}
        <x-card>
            <x-slot:header>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="section-title">Detail Item Opname</div>
                    @if($discrepancies > 0)
                    <span class="badge bg-danger">{{ $discrepancies }} selisih</span>
                    @endif
                </div>
            </x-slot:header>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:.82rem;">
                    <thead>
                        <tr>
                            <th>BinLoc</th><th>Part No.</th><th>Nama Part</th><th>UOM</th>
                            <th class="text-center">Sistem</th><th class="text-center">Fisik</th>
                            <th class="text-center">Selisih</th><th>Catatan</th><th>Dihitung oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="{{ $item->difference < 0 ? 'table-danger' : ($item->difference > 0 ? 'table-success' : '') }}">
                            <td><span class="badge bg-dark" style="font-size:.7rem;">{{ $item->sparepart->bin_location ?: '-' }}</span></td>
                            <td style="font-weight:600;">{{ $item->sparepart->part_number }}</td>
                            <td>{{ $item->sparepart->part_name }}</td>
                            <td class="text-muted">{{ $item->sparepart->uom }}</td>
                            <td class="text-center">{{ $item->system_qty }}</td>
                            <td class="text-center"><strong>{{ $item->physical_qty }}</strong></td>
                            <td class="text-center">
                                <strong class="{{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                </strong>
                            </td>
                            <td class="text-muted">{{ $item->notes ?? '-' }}</td>
                            <td style="font-size:.75rem;" class="text-muted">
                                {{ $item->counter->name ?? '-' }}<br>
                                {{ $item->counted_at?->format('d/m H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
// Live diff calculation as user types
document.addEventListener('input', function(e) {
    if (!e.target.classList.contains('physical-qty')) return;
    const input = e.target;
    const sysQty = parseInt(input.dataset.system) || 0;
    const physQty = input.value !== '' ? parseInt(input.value) : null;
    const diffCell = document.getElementById('diff-' + input.dataset.row);
    if (!diffCell) return;

    if (physQty === null || isNaN(physQty)) {
        diffCell.innerHTML = '<span class="text-muted">-</span>';
        return;
    }
    const diff = physQty - sysQty;
    const cls = diff > 0 ? 'text-success' : (diff < 0 ? 'text-danger' : 'text-muted');
    diffCell.innerHTML = `<strong class="${cls}">${diff > 0 ? '+' : ''}${diff}</strong>`;
});

// Filter rows by type
function filterItems(type) {
    const rows = document.querySelectorAll('#opnameTable tbody tr');
    let visible = 0;
    rows.forEach(row => {
        const show = type === 'all' || row.dataset.filter === type;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('visibleCount').textContent = visible;
    ['filterAll','filterUncounted','filterDisc'].forEach(id => document.getElementById(id)?.classList.remove('active'));
    const map = {all:'filterAll', uncounted:'filterUncounted', discrepancy:'filterDisc'};
    document.getElementById(map[type])?.classList.add('active');
}
</script>
@endpush
