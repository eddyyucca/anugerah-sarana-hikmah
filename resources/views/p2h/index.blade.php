@extends('layouts.app')
@section('page-title', 'P2H Check')
@section('breadcrumb')<li class="breadcrumb-item active">P2H Check</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title"><i class="bi bi-clipboard-check me-2"></i>P2H Inspection List</div>
        <div class="d-flex gap-2">
            <a href="{{ route('p2h.summary') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:12px;"><i class="bi bi-bar-chart me-1"></i>Summary</a>
            <a href="{{ route('p2h.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> New P2H</a>
        </div>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="P2H / Unit / Operator..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Status</option>
                    <option value="layak" {{ request('status')=='layak'?'selected':'' }}>Layak</option>
                    <option value="layak_catatan" {{ request('status')=='layak_catatan'?'selected':'' }}>Layak + Catatan</option>
                    <option value="tidak_layak" {{ request('status')=='tidak_layak'?'selected':'' }}>Tidak Layak</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="unit_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Unit</option>
                    @foreach($units as $u)<option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->unit_code }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button> <a href="{{ route('p2h.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>P2H Number</th><th>Unit</th><th>Operator</th><th>Date</th><th>Shift</th><th>HM</th><th>Items</th><th>Status</th><th>Reviewed</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($checks as $c)
                    <tr>
                        <td><a href="{{ route('p2h.show', $c) }}" style="font-weight:700;">{{ $c->p2h_number }}</a></td>
                        <td>{{ $c->unit->unit_code ?? '-' }} <span class="text-muted" style="font-size:.75rem;">{{ $c->unit->unit_model ?? '' }}</span></td>
                        <td>{{ $c->operator->operator_name ?? '-' }}</td>
                        <td>{{ $c->check_date->format('d M Y') }}</td>
                        <td><span class="badge {{ $c->shift === 'day' ? 'badge-soft-warning' : 'badge-soft-info' }}" style="border-radius:999px;">{{ ucfirst($c->shift) }}</span></td>
                        <td>{{ number_format($c->hour_meter_start, 0) }}</td>
                        <td>{{ $c->items_count }}</td>
                        <td>@include('components.p2h-status', ['status' => $c->overall_status])</td>
                        <td>
                            @if($c->reviewed_at) <i class="bi bi-check-circle text-success"></i>
                            @else <span class="text-muted" style="font-size:.78rem;">Pending</span> @endif
                        </td>
                        <td><a href="{{ route('p2h.show', $c) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-clipboard-x fs-3 d-block mb-2"></i>No P2H records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $checks->links() }}</div>
    </div>
</div>
@endsection
