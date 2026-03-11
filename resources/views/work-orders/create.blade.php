@extends('layouts.app')
@section('page-title', 'Create Work Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">Create</li>@endsection

@section('content')
<form action="{{ route('work-orders.store') }}" method="POST">
    @csrf
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">Work Order Details</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">WO Number</label><input type="text" class="form-control" value="{{ $woNumber }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required style="border-radius:10px;">
                        <option value="">-- Select Unit --</option>
                        @foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }} - {{ $u->unit_model }}</option>@endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                    <select name="maintenance_type" class="form-select" required style="border-radius:10px;">
                        <option value="corrective" {{ old('maintenance_type')=='corrective'?'selected':'' }}>Corrective</option>
                        <option value="preventive" {{ old('maintenance_type')=='preventive'?'selected':'' }}>Preventive</option>
                        <option value="predictive" {{ old('maintenance_type')=='predictive'?'selected':'' }}>Predictive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Technician</label>
                    <select name="technician_id" class="form-select" style="border-radius:10px;">
                        <option value="">-- Assign Later --</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('technician_id')==$t->id?'selected':'' }}>{{ $t->technician_code }} - {{ $t->technician_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Start Time <span class="text-danger">*</span></label><input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required style="border-radius:10px;"></div>
                <div class="col-md-9"><label class="form-label">Complaint <span class="text-danger">*</span></label><textarea name="complaint" class="form-control @error('complaint') is-invalid @enderror" rows="2" required style="border-radius:10px;">{{ old('complaint') }}</textarea>@error('complaint')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-3"><label class="form-label">Labor Cost</label><input type="number" step="0.01" name="labor_cost" class="form-control" value="{{ old('labor_cost', 0) }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Vendor Cost</label><input type="number" step="0.01" name="vendor_cost" class="form-control" value="{{ old('vendor_cost', 0) }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Consumable Cost</label><input type="number" step="0.01" name="consumable_cost" class="form-control" value="{{ old('consumable_cost', 0) }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Create WO</button>
    <a href="{{ route('work-orders.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
</form>
@endsection
