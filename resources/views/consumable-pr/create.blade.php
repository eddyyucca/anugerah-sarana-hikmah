@extends('layouts.app')
@section('page-title', 'Create Consumable PR')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('consumable-pr.index') }}">Consumable PR</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
@if($spareparts->isEmpty())
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>No consumable spareparts found. Mark spareparts as consumable in Sparepart Master first.
</div>
@else
<form action="{{ route('consumable-pr.store') }}" method="POST" id="consumablePrForm">
    @csrf

    {{-- PR Header --}}
    <div class="erp-card mb-3">
        <div class="erp-card-header">
            <div class="section-title"><i class="bi bi-droplet me-2"></i>Consumable PR Header</div>
        </div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">PR Number</label>
                    <input type="text" class="form-control" value="{{ $prNumber }}" readonly style="background:#f8f9fa;">
                </div>
                <x-form-date name="request_date" label="Request Date" :value="date('Y-m-d')" required class="col-md-3" />
                <x-form-input name="remarks" label="Remarks" type="text" placeholder="Consumable restock..." class="col-md-6" />
            </div>
        </div>
    </div>

    {{-- Auto-suggested low stock items --}}
    @if($lowStockItems->isNotEmpty())
    <div class="erp-card mb-3 border-warning">
        <div class="erp-card-header" style="background:linear-gradient(135deg,#fff3cd,#ffeeba);">
            <div class="d-flex justify-content-between align-items-center">
                <div class="section-title text-warning-emphasis">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                    Auto-Suggested Low Stock Items
                    <span class="badge bg-warning text-dark ms-2">{{ $lowStockItems->count() }} items</span>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggleAllSuggested" checked>
                    <label class="form-check-label small" for="toggleAllSuggested">Include all</label>
                </div>
            </div>
            <div class="small text-muted mt-1">Items below minimum stock level — automatically suggested for reorder. Adjust qty or uncheck to exclude.</div>
        </div>
        <div class="erp-card-body p-0">
            <table class="table table-modern mb-0" id="suggestedTable">
                <thead>
                    <tr>
                        <th style="width:40px;"><i class="bi bi-check2-square"></i></th>
                        <th>Part</th>
                        <th style="width:90px;">Current Stock</th>
                        <th style="width:90px;">Min Stock</th>
                        <th style="width:90px;">Deficit</th>
                        <th style="width:120px;">Order Qty</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $item)
                    @php $deficit = max(0, $item->minimum_stock - $item->stock_on_hand); @endphp
                    <tr class="suggested-row" data-id="{{ $item->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input suggested-check" checked
                                data-row="{{ $loop->index }}">
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $item->part_name }}</div>
                            <div class="small text-muted">{{ $item->part_number }} &bull; {{ $item->uom }}</div>
                            <input type="hidden" class="suggested-sparepart-id" name="suggested[{{ $loop->index }}][sparepart_id]" value="{{ $item->id }}">
                        </td>
                        <td>
                            <span class="{{ $item->stock_on_hand == 0 ? 'text-danger fw-bold' : 'text-warning fw-semibold' }}">
                                {{ $item->stock_on_hand }}
                            </span>
                            @if($item->stock_on_hand == 0)
                                <span class="badge bg-danger ms-1" style="font-size:10px;">HABIS</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">LOW</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $item->minimum_stock }}</td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                -{{ $deficit }}
                            </span>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm suggested-qty"
                                name="suggested[{{ $loop->index }}][qty]"
                                value="{{ $item->suggested_qty }}" min="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                name="suggested[{{ $loop->index }}][notes]"
                                placeholder="Optional...">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-success mb-3">
        <i class="bi bi-check-circle me-2"></i>All consumable stock levels are sufficient. You can still add items manually below.
    </div>
    @endif

    {{-- Manual add items --}}
    <div class="erp-card mb-3">
        <div class="erp-card-header d-flex justify-content-between align-items-center">
            <div class="section-title"><i class="bi bi-plus-circle me-2"></i>Additional Items <span class="small text-muted fw-normal">(manual)</span></div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addManualItem()">
                <i class="bi bi-plus-lg me-1"></i>Add Item
            </button>
        </div>
        <div class="erp-card-body">
            <table class="table table-modern mb-0" id="manualTable">
                <thead>
                    <tr>
                        <th>Consumable Part</th>
                        <th style="width:90px;">Stock</th>
                        <th style="width:90px;">Min</th>
                        <th style="width:120px;">Order Qty</th>
                        <th>Notes</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody id="manualBody">
                    {{-- rows added dynamically --}}
                </tbody>
            </table>
            <div id="manualEmpty" class="text-center text-muted py-3 small">
                <i class="bi bi-info-circle me-1"></i>Click "Add Item" to add a consumable not listed above.
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-save me-1"></i>Save Consumable PR
        </button>
        <a href="{{ route('consumable-pr.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
const allParts = @json($spareparts);
const autoIds  = new Set(@json($lowStockItems->pluck('id')));
let mi = 0; // manual row index

function buildPartOptions(excludeIds = []) {
    return allParts
        .filter(p => !excludeIds.includes(p.id))
        .map(p => `<option value="${p.id}" data-stk="${p.stock_on_hand}" data-min="${p.minimum_stock}">${p.part_number} - ${p.part_name} (${p.uom})</option>`)
        .join('');
}

function getExcludedIds() {
    // IDs already in the suggested table (checked) + manual table
    const checked = [...document.querySelectorAll('.suggested-check:checked')]
        .map(cb => parseInt(cb.closest('tr').dataset.id));
    const manual = [...document.querySelectorAll('#manualBody .sp-sel')]
        .map(sel => parseInt(sel.value)).filter(Boolean);
    return [...checked, ...manual];
}

function addManualItem() {
    const opts = buildPartOptions(getExcludedIds());
    const tbody = document.getElementById('manualBody');
    tbody.insertAdjacentHTML('beforeend', `<tr data-mi="${mi}">
        <td><select name="manual[${mi}][sparepart_id]" class="form-select form-select-sm tom-select sp-sel" data-mi="${mi}" required>
            <option value="">-- Select Consumable --</option>${opts}
        </select></td>
        <td class="stk-val text-center" data-mi="${mi}">-</td>
        <td class="min-val text-center" data-mi="${mi}">-</td>
        <td><input type="number" name="manual[${mi}][qty]" class="form-control form-control-sm" min="1" value="1" required></td>
        <td><input type="text" name="manual[${mi}][notes]" class="form-control form-control-sm" placeholder="Optional..."></td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="removeManual(this)"><i class="bi bi-x-lg"></i></button></td>
    </tr>`);

    // init tom-select on new row
    const sel = tbody.querySelector(`[data-mi="${mi}"]`);
    if (window.TomSelect) new TomSelect(sel, { maxOptions: 200 });

    mi++;
    updateManualEmpty();
}

function removeManual(btn) {
    btn.closest('tr').remove();
    updateManualEmpty();
}

function updateManualEmpty() {
    const empty = document.getElementById('manualEmpty');
    const rows  = document.querySelectorAll('#manualBody tr').length;
    empty.style.display = rows > 0 ? 'none' : '';
}

// Update stock/min display when manual part selected
document.addEventListener('change', e => {
    if (e.target.matches('.sp-sel')) {
        const o  = e.target.selectedOptions[0];
        const mi = e.target.dataset.mi;
        document.querySelector(`.stk-val[data-mi="${mi}"]`).textContent = o?.dataset?.stk ?? '-';
        document.querySelector(`.min-val[data-mi="${mi}"]`).textContent = o?.dataset?.min ?? '-';
    }
});

// Toggle all suggested
const toggleAll = document.getElementById('toggleAllSuggested');
if (toggleAll) {
    toggleAll.addEventListener('change', () => {
        document.querySelectorAll('.suggested-check').forEach(cb => {
            cb.checked = toggleAll.checked;
            toggleSuggestedRow(cb);
        });
    });
}

document.addEventListener('change', e => {
    if (e.target.matches('.suggested-check')) toggleSuggestedRow(e.target);
});

function toggleSuggestedRow(cb) {
    const row = cb.closest('tr');
    const inputs = row.querySelectorAll('input:not(.form-check-input)');
    inputs.forEach(inp => inp.disabled = !cb.checked);
    row.style.opacity = cb.checked ? '1' : '0.4';
    // Update suggested-sparepart-id disabled state to exclude from POST
    row.querySelector('.suggested-sparepart-id').disabled = !cb.checked;
    row.querySelector('.suggested-qty').disabled = !cb.checked;
}

// Before submit: merge suggested (checked) + manual into items[]
document.getElementById('consumablePrForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const fd = new FormData(this);
    const token = fd.get('_token');
    const date  = fd.get('request_date');
    const rmk   = fd.get('remarks');

    let items = [];

    // Collect checked suggested rows
    document.querySelectorAll('.suggested-row').forEach(row => {
        const cb = row.querySelector('.suggested-check');
        if (!cb.checked) return;
        const sid  = row.querySelector('.suggested-sparepart-id').value;
        const qty  = row.querySelector('.suggested-qty').value;
        const notes = row.querySelector('input[name*="[notes]"]').value;
        items.push({ sparepart_id: sid, qty, notes });
    });

    // Collect manual rows
    document.querySelectorAll('#manualBody tr').forEach(row => {
        const sid   = row.querySelector('.sp-sel')?.value;
        const qty   = row.querySelector('input[type="number"]')?.value;
        const notes = row.querySelector('input[type="text"]')?.value || '';
        if (sid) items.push({ sparepart_id: sid, qty, notes });
    });

    if (items.length === 0) {
        alert('Please include at least one item.');
        return;
    }

    // Build a clean form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = this.action;
    form.innerHTML = `<input name="_token" value="${token}">
        <input name="request_date" value="${date}">
        <input name="remarks" value="${rmk ?? ''}">`;

    items.forEach((item, i) => {
        form.innerHTML += `<input name="items[${i}][sparepart_id]" value="${item.sparepart_id}">
            <input name="items[${i}][qty]" value="${item.qty}">
            <input name="items[${i}][notes]" value="${item.notes}">`;
    });

    document.body.appendChild(form);
    form.submit();
});
</script>
@endpush
