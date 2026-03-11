@extends('layouts.app')
@section('page-title', 'Unit Availability Report')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Availability</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title">Unit Availability</div></div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="unit_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Units</option>
                    @foreach($allUnits as $u)<option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Unit Code</th><th>Model</th><th>Category</th><th>Status</th><th>Avg Availability</th><th>Total Downtime (hrs)</th></tr></thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td><strong>{{ $unit->unit_code }}</strong></td>
                        <td>{{ $unit->unit_model }}</td>
                        <td>{{ $unit->category->name ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $unit->current_status])</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-modern flex-grow-1"><div class="progress-bar" style="width:{{ $unit->avg_availability }}%"></div></div>
                                <span style="font-size:.85rem;font-weight:700;">{{ $unit->avg_availability }}%</span>
                            </div>
                        </td>
                        <td>{{ $unit->total_downtime }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
