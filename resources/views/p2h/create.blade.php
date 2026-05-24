@extends('layouts.app')
@section('page-title', 'New P2H Inspection')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('p2h.index') }}">P2H Check</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('p2h.store') }}" method="POST" id="p2hForm">
    @csrf
    {{-- Header --}}
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-clipboard-check me-2"></i>P2H Header</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-2">
                <x-form-group label="P2H Number">
                    <input type="text" class="form-control" value="{{ $p2hNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Unit (Available Only)" required>
                    <select name="unit_id" class="form-select tom-select @error('unit_id') is-invalid @enderror" required id="unitSelect">
                        <option value="">-- Select Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}" data-hm="{{ $u->hour_meter }}" data-odo="{{ $u->current_odometer }}">{{ $u->unit_code }} - {{ $u->unit_model }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Operator" required>
                    <select name="operator_id" class="form-select tom-select @error('operator_id') is-invalid @enderror" required>
                        <option value="">-- Select Operator --</option>
                        @foreach($operators as $op)
                        <option value="{{ $op->id }}">{{ $op->operator_code }} - {{ $op->operator_name }}</option>
                        @endforeach
                    </select>
                    @error('operator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Date" required>
                    <input type="date" name="check_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Shift" required>
                    <select name="shift" class="form-select tom-select" required>
                        <option value="day">Shift Pagi</option>
                        <option value="night">Shift Malam</option>
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Hour Meter">
                    <input type="number" step="0.1" name="hour_meter_start" id="hmInput"
                        class="form-control @error('hour_meter_start') is-invalid @enderror"
                        value="{{ old('hour_meter_start', 0) }}" min="0">
                    <small id="hmHint" class="text-muted"></small>
                    @error('hour_meter_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Odometer (KM)">
                    <input type="number" step="0.1" name="km_start" id="kmInput" class="form-control @error('km_start') is-invalid @enderror"
                        value="{{ old('km_start', 0) }}" min="0" style="border-color:#3b82f6;">
                    <small id="odoHint" class="text-primary" style="font-size:.78rem;font-weight:600;"></small>
                    @error('km_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-8">
                <x-form-group label="General Notes">
                    <input type="text" name="general_notes" class="form-control" placeholder="Catatan umum...">
                </x-form-group>
            </div>
        </div>
    </x-card>

    {{-- Checklist Items --}}
    @php $idx = 0; @endphp
    @foreach($checklist as $category => $items)
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">
                <i class="bi bi-check2-square me-2"></i>{{ $category }}
            </div>
        </x-slot:header>
        @foreach($items as $item)
        <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
            <input type="hidden" name="items[{{ $idx }}][category]" value="{{ $category }}">
            <input type="hidden" name="items[{{ $idx }}][check_item]" value="{{ $item }}">
            <div class="flex-grow-1">
                <div style="font-weight:600;font-size:.9rem;">{{ $item }}</div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_good" value="good" checked>
                <label class="btn btn-sm btn-outline-success" for="c{{ $idx }}_good" style="font-size:.75rem;padding:.25rem .5rem;">
                    <i class="bi bi-check-lg"></i> Good
                </label>

                <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_warn" value="warning">
                <label class="btn btn-sm btn-outline-warning" for="c{{ $idx }}_warn" style="font-size:.75rem;padding:.25rem .5rem;">
                    <i class="bi bi-exclamation-triangle"></i> Warning
                </label>

                <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_bad" value="bad">
                <label class="btn btn-sm btn-outline-danger" for="c{{ $idx }}_bad" style="font-size:.75rem;padding:.25rem .5rem;">
                    <i class="bi bi-x-lg"></i> Bad
                </label>

                <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_na" value="na">
                <label class="btn btn-sm btn-outline-secondary" for="c{{ $idx }}_na" style="font-size:.75rem;padding:.25rem .5rem;">
                    N/A
                </label>
            </div>
            <div style="width:200px;" class="flex-shrink-0">
                <input type="text" name="items[{{ $idx }}][notes]" class="form-control form-control-sm" placeholder="Notes...">
            </div>
        </div>
        @php $idx++; @endphp
        @endforeach
    </x-card>
    @endforeach

    <div class="d-flex gap-2">
        <x-button type="submit" variant="danger"><i class="bi bi-clipboard-check me-1"></i>Submit P2H</x-button>
        <a href="{{ route('p2h.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('unitSelect').addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    const hmInput  = document.getElementById('hmInput');
    const hmHint   = document.getElementById('hmHint');
    const kmInput  = document.getElementById('kmInput');
    const odoHint  = document.getElementById('odoHint');

    if (opt && opt.value) {
        const minHm  = parseFloat(opt.dataset.hm)  || 0;
        const minOdo = parseFloat(opt.dataset.odo)  || 0;

        hmInput.value = minHm;
        hmInput.min   = minHm;
        hmHint.textContent = 'HM saat ini: ' + minHm.toLocaleString('id-ID');

        kmInput.value = minOdo;
        kmInput.min   = minOdo;
        odoHint.textContent = 'ODO saat ini: ' + minOdo.toLocaleString('id-ID') + ' km';
    } else {
        hmInput.min = 0;
        hmHint.textContent = '';
        kmInput.min = 0;
        odoHint.textContent = '';
    }
});

document.getElementById('hmInput').addEventListener('input', function() {
    const opt = document.getElementById('unitSelect').selectedOptions[0];
    if (!opt || !opt.dataset.hm) return;
    const minHm = parseFloat(opt.dataset.hm) || 0;
    if (parseFloat(this.value) < minHm) {
        this.setCustomValidity('HM tidak boleh kurang dari ' + minHm);
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

document.getElementById('kmInput').addEventListener('input', function() {
    const opt = document.getElementById('unitSelect').selectedOptions[0];
    if (!opt || !opt.dataset.odo) return;
    const minOdo = parseFloat(opt.dataset.odo) || 0;
    if (parseFloat(this.value) < minOdo) {
        this.setCustomValidity('Odometer tidak boleh kurang dari ' + minOdo.toLocaleString('id-ID') + ' km');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});
</script>
@endpush
