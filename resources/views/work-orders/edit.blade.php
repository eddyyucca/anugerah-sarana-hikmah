@extends('layouts.app')
@section('page-title', 'Edit Work Order')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">Edit</li>@endsection

@section('content')
<form action="{{ route('work-orders.update', $workOrder) }}" method="POST">
    @csrf @method('PUT')
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
</form>
@endsection
