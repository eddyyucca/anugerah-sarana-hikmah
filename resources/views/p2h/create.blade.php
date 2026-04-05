@extends('layouts.app')
@section('page-title', 'New P2H Inspection')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('p2h.index') }}">P2H Check</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('p2h.store') }}" method="POST" id="p2hForm">
    @csrf
    {{-- Header --}}
<<<<<<< HEAD
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
=======
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title"><i class="bi bi-clipboard-check me-2"></i>P2H Header</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">P2H Number</label>
                    <input type="text" class="form-control" value="{{ $p2hNumber }}" readonly style="border-radius:10px;background:#f8f9fa;">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit (Available Only) <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required style="border-radius:10px;" id="unitSelect">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
                        <option value="">-- Select Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}" data-hm="{{ $u->hour_meter }}">{{ $u->unit_code }} - {{ $u->unit_model }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
<<<<<<< HEAD
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Operator" required>
                    <select name="operator_id" class="form-select tom-select @error('operator_id') is-invalid @enderror" required>
=======
                </div>
                <div class="col-md-3">
                    <label class="form-label">Operator <span class="text-danger">*</span></label>
                    <select name="operator_id" class="form-select @error('operator_id') is-invalid @enderror" required style="border-radius:10px;">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
                        <option value="">-- Select Operator --</option>
                        @foreach($operators as $op)
                        <option value="{{ $op->id }}">{{ $op->operator_code }} - {{ $op->operator_name }}</option>
                        @endforeach
                    </select>
                    @error('operator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
<<<<<<< HEAD
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
                        <option value="day">Day Shift</option>
                        <option value="night">Night Shift</option>
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="Hour Meter">
                    <input type="number" step="0.1" name="hour_meter_start" id="hmInput" class="form-control" value="0">
                </x-form-group>
            </div>
            <div class="col-md-2">
                <x-form-group label="KM Start">
                    <input type="number" step="0.1" name="km_start" class="form-control" value="0">
                </x-form-group>
            </div>
            <div class="col-md-8">
                <x-form-group label="General Notes">
                    <input type="text" name="general_notes" class="form-control" placeholder="Catatan umum...">
                </x-form-group>
            </div>
        </div>
    </x-card>
=======
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="check_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:10px;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                    <select name="shift" class="form-select" required style="border-radius:10px;">
                        <option value="day">Day Shift</option>
                        <option value="night">Night Shift</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hour Meter</label>
                    <input type="number" step="0.1" name="hour_meter_start" id="hmInput" class="form-control" value="0" style="border-radius:10px;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">KM Start</label>
                    <input type="number" step="0.1" name="km_start" class="form-control" value="0" style="border-radius:10px;">
                </div>
                <div class="col-md-8">
                    <label class="form-label">General Notes</label>
                    <input type="text" name="general_notes" class="form-control" placeholder="Catatan umum..." style="border-radius:10px;">
                </div>
            </div>
        </div>
    </div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c

    {{-- Checklist Items --}}
    @php $idx = 0; @endphp
    @foreach($checklist as $category => $items)
<<<<<<< HEAD
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
=======
    <div class="erp-card mb-3">
        <div class="erp-card-header">
            <div class="section-title">
                <i class="bi bi-check2-square me-2"></i>{{ $category }}
            </div>
        </div>
        <div class="erp-card-body">
            @foreach($items as $item)
            <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <input type="hidden" name="items[{{ $idx }}][category]" value="{{ $category }}">
                <input type="hidden" name="items[{{ $idx }}][check_item]" value="{{ $item }}">
                <div class="flex-grow-1">
                    <div style="font-weight:600;font-size:.9rem;">{{ $item }}</div>
                </div>
                <div class="d-flex gap-1 flex-shrink-0">
                    <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_good" value="good" checked>
                    <label class="btn btn-sm btn-outline-success" for="c{{ $idx }}_good" style="border-radius:8px;font-size:.75rem;padding:.25rem .5rem;">
                        <i class="bi bi-check-lg"></i> Good
                    </label>

                    <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_warn" value="warning">
                    <label class="btn btn-sm btn-outline-warning" for="c{{ $idx }}_warn" style="border-radius:8px;font-size:.75rem;padding:.25rem .5rem;">
                        <i class="bi bi-exclamation-triangle"></i> Warning
                    </label>

                    <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_bad" value="bad">
                    <label class="btn btn-sm btn-outline-danger" for="c{{ $idx }}_bad" style="border-radius:8px;font-size:.75rem;padding:.25rem .5rem;">
                        <i class="bi bi-x-lg"></i> Bad
                    </label>

                    <input type="radio" class="btn-check" name="items[{{ $idx }}][condition]" id="c{{ $idx }}_na" value="na">
                    <label class="btn btn-sm btn-outline-secondary" for="c{{ $idx }}_na" style="border-radius:8px;font-size:.75rem;padding:.25rem .5rem;">
                        N/A
                    </label>
                </div>
                <div style="width:200px;" class="flex-shrink-0">
                    <input type="text" name="items[{{ $idx }}][notes]" class="form-control form-control-sm" placeholder="Notes..." style="border-radius:8px;">
                </div>
            </div>
            @php $idx++; @endphp
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger" style="border-radius:12px;"><i class="bi bi-clipboard-check me-1"></i>Submit P2H</button>
        <a href="{{ route('p2h.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('unitSelect').addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    if (opt && opt.dataset.hm) {
        document.getElementById('hmInput').value = opt.dataset.hm;
    }
});
</script>
@endpush
