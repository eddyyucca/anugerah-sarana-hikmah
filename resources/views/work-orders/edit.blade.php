@extends('layouts.app')
@section('page-title', 'Edit Work Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">Edit</li>@endsection

@section('content')
<form action="{{ route('work-orders.update', $workOrder) }}" method="POST">
    @csrf @method('PUT')
<<<<<<< HEAD
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title">Edit: {{ $workOrder->wo_number }}</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="WO Number">
                    <input type="text" class="form-control" value="{{ $workOrder->wo_number }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Maintenance Type">
                    <select name="maintenance_type" class="form-select tom-select" required>
                        @foreach(['corrective','preventive','predictive'] as $mt)<option value="{{ $mt }}" {{ $workOrder->maintenance_type==$mt?'selected':'' }}>{{ ucfirst($mt) }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Technician">
                    <select name="technician_id" class="form-select tom-select">
                        <option value="">-- None --</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ $workOrder->technician_id==$t->id?'selected':'' }}>{{ $t->technician_name }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-12">
                <x-form-group label="Complaint">
                    <textarea name="complaint" class="form-control" rows="2" required>{{ $workOrder->complaint }}</textarea>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Type of Complaint" required>
                    <select name="complaint_type_id" class="form-select tom-select" required>
                        <option value="">-- Select Type --</option>
                        @foreach($complaintTypes as $ct)<option value="{{ $ct->id }}" {{ $workOrder->complaint_type_id==$ct->id?'selected':'' }}>{{ $ct->name }}</option>@endforeach
                    </select>
                </x-form-group>
            </div>
            <div class="col-md-12">
                <x-form-group label="Action Taken">
                    <textarea name="action_taken" class="form-control" rows="2">{{ $workOrder->action_taken }}</textarea>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Labor Cost">
                    <input type="number" step="0.01" name="labor_cost" class="form-control" value="{{ $workOrder->labor_cost }}">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Vendor Cost">
                    <input type="number" step="0.01" name="vendor_cost" class="form-control" value="{{ $workOrder->vendor_cost }}">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Consumable Cost">
                    <input type="number" step="0.01" name="consumable_cost" class="form-control" value="{{ $workOrder->consumable_cost }}">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Remarks">
                    <input type="text" name="remarks" class="form-control" value="{{ $workOrder->remarks }}">
                </x-form-group>
            </div>
        </div>
    </x-card>
    <x-button type="submit" variant="danger">Update WO</x-button>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-light">Cancel</a>
=======
    <div class="erp-card mb-3">
        <div class="erp-card-header"><div class="section-title">Edit: {{ $workOrder->wo_number }}</div></div>
        <div class="erp-card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">WO Number</label><input type="text" class="form-control" value="{{ $workOrder->wo_number }}" readonly style="border-radius:10px;background:#f8f9fa;"></div>
                <div class="col-md-3">
                    <label class="form-label">Maintenance Type</label>
                    <select name="maintenance_type" class="form-select" required style="border-radius:10px;">
                        @foreach(['corrective','preventive','predictive'] as $mt)<option value="{{ $mt }}" {{ $workOrder->maintenance_type==$mt?'selected':'' }}>{{ ucfirst($mt) }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Technician</label>
                    <select name="technician_id" class="form-select" style="border-radius:10px;">
                        <option value="">-- None --</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ $workOrder->technician_id==$t->id?'selected':'' }}>{{ $t->technician_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-12"><label class="form-label">Complaint</label><textarea name="complaint" class="form-control" rows="2" required style="border-radius:10px;">{{ $workOrder->complaint }}</textarea></div>
                <div class="col-md-12"><label class="form-label">Action Taken</label><textarea name="action_taken" class="form-control" rows="2" style="border-radius:10px;">{{ $workOrder->action_taken }}</textarea></div>
                <div class="col-md-3"><label class="form-label">Labor Cost</label><input type="number" step="0.01" name="labor_cost" class="form-control" value="{{ $workOrder->labor_cost }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Vendor Cost</label><input type="number" step="0.01" name="vendor_cost" class="form-control" value="{{ $workOrder->vendor_cost }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Consumable Cost</label><input type="number" step="0.01" name="consumable_cost" class="form-control" value="{{ $workOrder->consumable_cost }}" style="border-radius:10px;"></div>
                <div class="col-md-3"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" value="{{ $workOrder->remarks }}" style="border-radius:10px;"></div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-danger" style="border-radius:12px;">Update WO</button>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
</form>
@endsection
