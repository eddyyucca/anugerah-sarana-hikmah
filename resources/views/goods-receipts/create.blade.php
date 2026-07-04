@extends('layouts.app')
@section('page-title', 'Create Goods Receipt')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Goods Receipts</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('goods-receipts.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">GR Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="GR Number">
                    <input type="text" class="form-control" value="{{ $grNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Source PO" required>
                    <select name="purchase_order_id" class="form-select tom-select" required id="poSelect">
                        <option value="">-- Select PO --</option>
                        @foreach($openPOs as $opo)
                        <option value="{{ $opo->id }}" {{ ($po && $po->id == $opo->id)?'selected':'' }}>{{ $opo->po_number }}</option>
                        @endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Receipt Date" required>
                    <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Remarks">
                    <input type="text" name="remarks" class="form-control">
                </x-form-group>
            </div>
        </div>
    </x-card>

    <x-card class="mb-3">
        <x-slot:header>
            <div>
                <div class="section-title">Items to Receive</div>
                <div class="section-subtitle">Only items with outstanding qty shown. Enter qty received (can be partial).</div>
            </div>
        </x-slot:header>
        <div class="table-responsive">
            <table class="table table-modern mb-0" id="itemsTable">
                <thead><tr>
                    <th>Part Number</th>
                    <th>Part Name</th>
                    <th class="text-center">Ordered</th>
                    <th class="text-center">Already Received</th>
                    <th class="text-center" style="color:#dc2626;">Outstanding</th>
                    <th>Location</th>
                    <th style="width:130px;">Qty to Receive</th>
                    <th>No. Seri Ban <small class="text-muted fw-normal">(jika ban)</small></th>
                </tr></thead>
                <tbody>
                    @if($po && $poItems->count() > 0)
                        @foreach($poItems as $i => $item)
                        @php
                            $isTire = stripos($item->sparepart->part_name ?? '', 'ban') !== false
                                   || stripos($item->sparepart->part_name ?? '', 'tire') !== false
                                   || stripos($item->sparepart->part_name ?? '', 'tyre') !== false;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $item->sparepart->part_number }}</strong>
                                <input type="hidden" name="items[{{ $i }}][sparepart_id]" value="{{ $item->sparepart_id }}">
                                <input type="hidden" name="items[{{ $i }}][po_item_id]" value="{{ $item->id }}">
                            </td>
                            <td>
                                {{ $item->sparepart->part_name }}
                                @if($isTire)
                                    <span class="badge bg-danger ms-1" style="font-size:.7rem;"><i class="bi bi-circle-fill me-1"></i>Ban</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-center text-success fw-bold">{{ $item->qty_received }}</td>
                            <td class="text-center text-danger fw-bold">{{ $item->qty_remaining }}</td>
                            <td>
                                <select name="items[{{ $i }}][warehouse_location_id]" class="form-select form-select-sm tom-select">
                                    <option value="">-- Default --</option>
                                    @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][qty_received]" id="qtyInput{{ $i }}"
                                    class="form-control form-control-sm" value="{{ $item->qty_remaining }}" min="0" max="{{ $item->qty_remaining }}" required
                                    @if($isTire) onInput="debouncedGenerate({{ $i }}, this.value)" @endif>
                            </td>
                            <td>
                                @if($isTire)
                                <div id="snContainer{{ $i }}" style="max-height:160px;overflow-y:auto;min-width:200px;"></div>
                                <small class="text-muted" style="font-size:.7rem;">
                                    <i class="bi bi-info-circle me-1"></i>Field menyesuaikan qty. Semua kotak opsional, boleh dikosongkan.
                                </small>
                                @else
                                <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center text-muted py-3">Select a PO to see outstanding items.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>

    <x-button type="submit" variant="danger">Save GR</x-button>
    <a href="{{ route('goods-receipts.index') }}" class="btn btn-light">Cancel</a>
</form>
@endsection

@push('scripts')
<script>
function makeSnInput(idx, n) {
    const input = document.createElement('input');
    input.type = 'text';
    input.name = `items[${idx}][serial_numbers][]`;
    input.className = 'form-control form-control-sm mb-1';
    input.placeholder = `SN #${n + 1} (opsional)`;
    input.style.fontSize = '.78rem';
    input.style.fontFamily = 'monospace';
    return input;
}

function generateSnFields(idx, qty) {
    const container = document.getElementById('snContainer' + idx);
    if (!container) return;
    qty = Math.max(0, parseInt(qty) || 0);

    // Only touch the delta (add/remove) instead of rebuilding everything, to avoid
    // unnecessary DOM churn that can confuse other scripts (e.g. tom-select) watching the page.
    const current = container.querySelectorAll('input').length;
    if (qty > current) {
        for (let n = current; n < qty; n++) container.appendChild(makeSnInput(idx, n));
    } else if (qty < current) {
        const inputs = container.querySelectorAll('input');
        for (let n = current - 1; n >= qty; n--) inputs[n].remove();
    }
}

const debouncedGenerate = (() => {
    let timers = {};
    return (idx, qty) => {
        clearTimeout(timers[idx]);
        timers[idx] = setTimeout(() => generateSnFields(idx, qty), 250);
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="snContainer"]').forEach(container => {
        const idx = container.id.replace('snContainer', '');
        const qtyInput = document.getElementById('qtyInput' + idx);
        if (qtyInput) generateSnFields(idx, qtyInput.value);
    });
});
</script>
@endpush

