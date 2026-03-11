@extends('layouts.app')
@section('page-title', 'Repair Cost Report')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Repair Cost</li>@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3"><div class="erp-card p-3"><div class="kpi-label">Sparepart Cost</div><div class="kpi-value" style="font-size:1.3rem;">{{ number_format($summary['sparepart'], 0, ',', '.') }}</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="erp-card p-3"><div class="kpi-label">Labor Cost</div><div class="kpi-value" style="font-size:1.3rem;">{{ number_format($summary['labor'], 0, ',', '.') }}</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="erp-card p-3"><div class="kpi-label">Vendor Cost</div><div class="kpi-value" style="font-size:1.3rem;">{{ number_format($summary['vendor'], 0, ',', '.') }}</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="erp-card p-3 summary-highlight"><div class="kpi-label">Total Cost</div><div class="kpi-value" style="font-size:1.3rem;">{{ number_format($summary['total'], 0, ',', '.') }}</div></div></div>
</div>

<div class="erp-card">
    <div class="erp-card-header"><div class="section-title">Repair Cost Details</div></div>
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
                <thead><tr><th>Unit</th><th>WO</th><th>Sparepart</th><th>Labor</th><th>Vendor</th><th>Consumable</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($costs as $c)
                    <tr>
                        <td>{{ $c->unit->unit_code ?? '-' }}</td>
                        <td><a href="{{ route('work-orders.show', $c->work_order_id) }}">{{ $c->workOrder->wo_number ?? '-' }}</a></td>
                        <td>{{ number_format($c->sparepart_cost, 0, ',', '.') }}</td>
                        <td>{{ number_format($c->labor_cost, 0, ',', '.') }}</td>
                        <td>{{ number_format($c->vendor_cost, 0, ',', '.') }}</td>
                        <td>{{ number_format($c->consumable_cost, 0, ',', '.') }}</td>
                        <td><strong>{{ number_format($c->total_cost, 0, ',', '.') }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No cost data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $costs->links() }}</div>
    </div>
</div>
@endsection
