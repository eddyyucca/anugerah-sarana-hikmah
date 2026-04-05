@extends('layouts.app')
@section('page-title', 'Complaint Analysis Report')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Complaint Analysis</li>@endsection

@section('content')
<x-card>
    <x-slot:header>
        <div class="section-title">Complaint Type Analysis</div>
    </x-slot:header>

    <!-- Filter Panel -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <label class="form-label fw-600 small">Complaint Type</label>
            <select name="complaint_type_id" class="form-select form-select-sm tom-select">
                <option value="">All Types</option>
                @foreach($allComplaintTypes as $ct)
                    <option value="{{ $ct->id }}" {{ request('complaint_type_id')==$ct->id?'selected':'' }}>{{ $ct->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-600 small">From Date</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-600 small">To Date</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-600 small">Maint. Type</label>
            <select name="maintenance_type" class="form-select form-select-sm tom-select">
                <option value="">All Types</option>
                <option value="corrective" {{ request('maintenance_type')=='corrective'?'selected':'' }}>Corrective</option>
                <option value="preventive" {{ request('maintenance_type')=='preventive'?'selected':'' }}>Preventive</option>
                <option value="predictive" {{ request('maintenance_type')=='predictive'?'selected':'' }}>Predictive</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-danger btn-sm">Filter</button>
            <a href="{{ route('reports.complaint-analysis') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row g-2 mb-4">
        @foreach($summary as $item)
        <?php $ct = $item->complaintType; ?>
        @if($ct)
        <div class="col-md-6 col-xl-4">
            <div class="erp-card" style="border-left: 4px solid {{ $ct->color ?? '#6c757d' }}">
                <div class="erp-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:.75rem; color:#6b7280; margin-bottom:.35rem;">{{ $ct->name }}</div>
                            <div style="font-size:1.75rem; font-weight:800; line-height:1;">{{ $item->total_count }}</div>
                            <div style="font-size:.72rem; color:#6b7280; margin-top:.45rem;">
                                <strong>Completed:</strong> {{ $item->completed_count }}
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:.85rem; color:#6b7280;">Total Cost</div>
                            <div style="font-size:1.1rem; font-weight:700; color:{{ $item->total_cost > 0 ? '#dc2626' : '#6b7280' }}">
                                {{ number_format($item->total_cost, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.72rem; color:#6b7280; margin-top:.35rem;">
                                Downtime: {{ number_format($item->total_downtime, 1) }} hrs
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Detailed List -->
    <div class="table-responsive">
        <table class="table table-modern mb-0">
            <thead>
                <tr>
                    <th>WO Number</th>
                    <th>Unit</th>
                    <th>Type</th>
                    <th style="max-width:180px;">Complaint</th>
                    <th>Maint. Type</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Cost</th>
                    <th>Downtime</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr>
                    <td><a href="{{ route('work-orders.show', $wo) }}" class="fw-bold text-decoration-none">{{ $wo->wo_number }}</a></td>
                    <td>{{ $wo->unit->unit_code ?? '-' }}</td>
                    <td>
                        @if($wo->complaintType)
                            <span class="badge" style="background-color:{{ $wo->complaintType->color }}; color:#fff; border-radius:4px; padding:.4rem .7rem; font-size:.75rem;">
                                {{ $wo->complaintType->name }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="max-width:180px; white-space:normal; font-size:.78rem;">{{ Str::limit($wo->complaint, 50) }}</td>
                    <td><span class="badge badge-soft-info" style="border-radius:4px;">{{ ucfirst($wo->maintenance_type) }}</span></td>
                    <td>{{ $wo->creator->name ?? '-' }}</td>
                    <td>@include('components.status-badge', ['status' => $wo->status])</td>
                    <td>
                        <span style="font-weight:700;">{{ number_format($wo->labor_cost + $wo->vendor_cost + $wo->consumable_cost, 0, ',', '.') }}</span>
                    </td>
                    <td>{{ number_format($wo->downtime_hours, 1) }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No work orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $workOrders->links() }}</div>
</x-card>
@endsection
