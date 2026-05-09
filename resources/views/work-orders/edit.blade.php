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
                <x-form-group label="Lokasi Perbaikan" required>
                    <select name="repair_location" class="form-select @error('repair_location') is-invalid @enderror" required>
                        <option value="di_workshop" {{ $workOrder->repair_location=='di_workshop'?'selected':'' }}>Di Workshop</option>
                        <option value="di_luar_workshop" {{ $workOrder->repair_location=='di_luar_workshop'?'selected':'' }}>Di Luar Workshop</option>
                    </select>
                    @error('repair_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="col-md-3">
                <x-form-group label="Operator (Yang Menggunakan Unit)">
                    <select name="operator_id" class="form-select tom-select">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($operators as $op)<option value="{{ $op->id }}" {{ $workOrder->operator_id==$op->id?'selected':'' }}>{{ $op->operator_code }} - {{ $op->operator_name }}</option>@endforeach
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
            <div class="col-md-9">
                <x-form-group label="Remark">
                    <textarea name="remarks" class="form-control" rows="3">{{ $workOrder->remarks }}</textarea>
                </x-form-group>
            </div>
        </div>
    </x-card>
    <x-button type="submit" variant="danger">Update WO</x-button>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-light">Cancel</a>
</form>
@endsection
