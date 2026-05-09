@extends('layouts.app')
@section('page-title', 'Buat Work Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">Buat</li>@endsection

@section('content')
{{-- Budget warning banner (hidden by default, muncul via JS saat unit dipilih) --}}
<div id="budget-warning" class="alert alert-danger d-none mb-3" style="border-radius:14px;" role="alert">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-exclamation-octagon-fill fs-4 mt-1 flex-shrink-0"></i>
        <div>
            <strong>Unit Telah Melampaui Budget Perbaikan Bulanan!</strong>
            <div id="budget-warning-detail" class="mt-1" style="font-size:.9rem;"></div>
            <div class="mt-2 p-2 rounded-2" style="background:rgba(255,255,255,.6);font-size:.85rem;">
                <i class="bi bi-shield-lock me-1"></i>
                Work Order ini akan otomatis berstatus <strong>Menunggu Persetujuan</strong> dan membutuhkan persetujuan dari level tertinggi sebelum dapat dilanjutkan.
            </div>
        </div>
    </div>
</div>

<form action="{{ route('work-orders.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">Detail Work Order</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="No. WO">
                    <input type="text" class="form-control" value="{{ $woNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Unit" required>
                    <select name="unit_id" id="unit_id" class="form-select tom-select @error('unit_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }} - {{ $u->unit_model }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Operator (Yang Menggunakan Unit)">
                    <select name="operator_id" class="form-select tom-select">
                        <option value="">-- Pilih Operator --</option>
                        @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ old('operator_id')==$op->id?'selected':'' }}>{{ $op->operator_code }} - {{ $op->operator_name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Operator yang sedang menggunakan unit saat kerusakan terjadi.</div>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Jenis Pemeliharaan" required>
                    <select name="maintenance_type" class="form-select tom-select" required>
                        <option value="corrective" {{ old('maintenance_type')=='corrective'?'selected':'' }}>Corrective</option>
                        <option value="preventive" {{ old('maintenance_type')=='preventive'?'selected':'' }}>Preventive</option>
                        <option value="predictive" {{ old('maintenance_type')=='predictive'?'selected':'' }}>Predictive</option>
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Lokasi Perbaikan" required>
                    <select name="repair_location" class="form-select @error('repair_location') is-invalid @enderror" required>
                        <option value="di_workshop" {{ old('repair_location','di_workshop')=='di_workshop'?'selected':'' }}>Di Workshop</option>
                        <option value="di_luar_workshop" {{ old('repair_location')=='di_luar_workshop'?'selected':'' }}>Di Luar Workshop</option>
                    </select>
                    @error('repair_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Teknisi">
                    <select name="technician_id" class="form-select tom-select">
                        <option value="">-- Tugaskan Nanti --</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('technician_id')==$t->id?'selected':'' }}>{{ $t->technician_code }} - {{ $t->technician_name }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Waktu Mulai" required>
                    <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Biaya Tenaga Kerja">
                    <input type="number" step="0.01" name="labor_cost" class="form-control" value="{{ old('labor_cost', 0) }}">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Biaya Vendor">
                    <input type="number" step="0.01" name="vendor_cost" class="form-control" value="{{ old('vendor_cost', 0) }}">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Biaya Consumable">
                    <input type="number" step="0.01" name="consumable_cost" class="form-control" value="{{ old('consumable_cost', 0) }}">
                </x-form-group>
            </div>
            <div class="col-md-9">
                <x-form-group label="Keterangan">
                    <textarea name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
                </x-form-group>
            </div>
        </div>
    </x-card>
    <x-button type="submit" variant="danger">Buat WO</x-button>
    <a href="{{ route('work-orders.index') }}" class="btn btn-light">Batal</a>
</form>

@push('scripts')
<script>
const unitBudgetData = @json($unitBudgetStatus);

document.addEventListener('DOMContentLoaded', function () {
    const unitSelect    = document.getElementById('unit_id');
    const warningBox    = document.getElementById('budget-warning');
    const warningDetail = document.getElementById('budget-warning-detail');

    function fmt(num) {
        return 'IDR ' + Math.round(num).toLocaleString('id-ID');
    }

    function checkBudget(unitId) {
        const status = unitBudgetData[unitId];
        if (status && status.is_over_budget) {
            const excess = status.used - status.limit;
            warningDetail.innerHTML =
                `Budget bulanan <strong>${fmt(status.limit)}</strong> sudah terpakai <strong>${fmt(status.used)}</strong> ` +
                `(lebih <strong class="text-danger">${fmt(excess)}</strong>). ` +
                `Sisa: <strong>${fmt(status.remaining)}</strong>.`;
            warningBox.classList.remove('d-none');
        } else {
            warningBox.classList.add('d-none');
        }
    }

    // Tom-select mengganti native select — perlu listen event 'change' di native element
    unitSelect.addEventListener('change', function () { checkBudget(this.value); });

    if (unitSelect.value) checkBudget(unitSelect.value);
});
</script>
@endpush
@endsection
