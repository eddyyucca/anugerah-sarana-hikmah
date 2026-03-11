@extends('layouts.app')
@section('page-title', $workOrder->wo_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li><li class="breadcrumb-item active">{{ $workOrder->wo_number }}</li>@endsection

@section('content')
<div class="row g-3">
    {{-- Left: Info & Actions --}}
    <div class="col-lg-4">
        <div class="erp-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><div style="font-weight:800;font-size:1.2rem;">{{ $workOrder->wo_number }}</div><div class="text-muted" style="font-size:.85rem;">{{ ucfirst($workOrder->maintenance_type) }}</div></div>
                @include('components.status-badge', ['status' => $workOrder->status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Unit</td><td><a href="{{ route('units.show', $workOrder->unit_id) }}">{{ $workOrder->unit->unit_code }}</a> - {{ $workOrder->unit->unit_model }}</td></tr>
                <tr><td class="text-muted">Technician</td><td>{{ $workOrder->technician->technician_name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Start</td><td>{{ $workOrder->start_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                <tr><td class="text-muted">End</td><td>{{ $workOrder->end_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                <tr><td class="text-muted">Downtime</td><td>{{ $workOrder->downtime_hours }} hours</td></tr>
                <tr><td class="text-muted">Complaint</td><td>{{ $workOrder->complaint }}</td></tr>
                @if($workOrder->action_taken)<tr><td class="text-muted">Action</td><td>{{ $workOrder->action_taken }}</td></tr>@endif
                @if($workOrder->remarks)<tr><td class="text-muted">Remarks</td><td>{{ $workOrder->remarks }}</td></tr>@endif
            </table>

            <div class="d-flex flex-wrap gap-2">
                @if($workOrder->status === 'open')
                    <form action="{{ route('work-orders.progress', $workOrder) }}" method="POST">@csrf<button class="btn btn-sm btn-warning" style="border-radius:10px;">Start Progress</button></form>
                @endif
                @if(in_array($workOrder->status, ['in_progress','waiting_part']))
                    <form action="{{ route('work-orders.complete', $workOrder) }}" method="POST">@csrf<button class="btn btn-sm btn-success" style="border-radius:10px;" onclick="return confirm('Complete this WO?')"><i class="bi bi-check-lg me-1"></i>Complete</button></form>
                @endif
                @if(!in_array($workOrder->status, ['completed','cancelled']))
                    <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">Edit</a>
                    <a href="{{ route('goods-issues.create', ['wo_id' => $workOrder->id]) }}" class="btn btn-sm btn-outline-danger" style="border-radius:10px;"><i class="bi bi-box-arrow-up me-1"></i>Issue Parts</a>
                @endif
            </div>
        </div>

        {{-- Cost Summary --}}
        <div class="erp-card p-3">
            <div class="section-title mb-3">Cost Summary</div>
            @php
                $spCost = $workOrder->costSummary->sparepart_cost ?? 0;
                $totalCost = ($spCost) + $workOrder->labor_cost + $workOrder->vendor_cost + $workOrder->consumable_cost;
            @endphp
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Sparepart</span><span>IDR {{ number_format($spCost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Labor</span><span>IDR {{ number_format($workOrder->labor_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Vendor</span><span>IDR {{ number_format($workOrder->vendor_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box mb-2"><div class="d-flex justify-content-between"><span class="text-muted">Consumable</span><span>IDR {{ number_format($workOrder->consumable_cost, 0, ',', '.') }}</span></div></div>
            <div class="summary-box summary-highlight"><div class="d-flex justify-content-between"><strong>Total</strong><strong>IDR {{ number_format($totalCost, 0, ',', '.') }}</strong></div></div>
        </div>
    </div>

    {{-- Right: GI & Logs --}}
    <div class="col-lg-8">
        <div class="erp-card mb-3">
            <div class="erp-card-header"><div class="section-title">Related Goods Issues</div></div>
            <div class="erp-card-body">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>GI Number</th><th>Date</th><th>Status</th><th>Items</th><th>Total</th></tr></thead>
                        <tbody>
                            @forelse($workOrder->goodsIssues as $gi)
                            <tr>
                                <td><a href="{{ route('goods-issues.show', $gi) }}">{{ $gi->gi_number }}</a></td>
                                <td>{{ $gi->issue_date->format('d M Y') }}</td>
                                <td>@include('components.status-badge', ['status' => $gi->status])</td>
                                <td>{{ $gi->items->count() }}</td>
                                <td>IDR {{ number_format($gi->items->sum('total_price'), 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No goods issues linked.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="erp-card">
            <div class="erp-card-header"><div class="section-title">Activity Log</div></div>
            <div class="erp-card-body">
                @forelse($workOrder->logs->sortByDesc('activity_time') as $log)
                <div class="activity-item mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="activity-code">{{ ucfirst(str_replace('_',' ',$log->activity_type)) }}</span>
                            <span class="activity-meta ms-2">{{ $log->creator->name ?? 'System' }}</span>
                        </div>
                        <span class="activity-meta">{{ $log->activity_time->format('d M Y H:i') }}</span>
                    </div>
                    @if($log->description)<div class="activity-meta mt-1">{{ $log->description }}</div>@endif
                </div>
                @empty
                <div class="text-center text-muted py-3">No activity logs.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
