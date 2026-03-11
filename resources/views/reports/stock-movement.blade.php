@extends('layouts.app')
@section('page-title', 'Stock Movement Report')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Stock Movement</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title">Stock Movement</div></div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="sparepart_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Spareparts</option>
                    @foreach($spareparts as $sp)<option value="{{ $sp->id }}" {{ request('sparepart_id')==$sp->id?'selected':'' }}>{{ $sp->part_number }} - {{ $sp->part_name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius:10px;"></div>
            <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="border-radius:10px;"></div>
            <div class="col-auto"><button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Date</th><th>Part Number</th><th>Part Name</th><th>Type</th><th>In</th><th>Out</th><th>Balance</th><th>Reference</th></tr></thead>
                <tbody>
                    @forelse($movements as $mv)
                    <tr>
                        <td>{{ $mv->movement_date->format('d M Y') }}</td>
                        <td>{{ $mv->sparepart->part_number ?? '-' }}</td>
                        <td>{{ $mv->sparepart->part_name ?? '-' }}</td>
                        <td>
                            @if($mv->movement_type === 'in')
                                <span class="badge badge-soft-success" style="border-radius:999px;">IN</span>
                            @else
                                <span class="badge badge-soft-danger" style="border-radius:999px;">OUT</span>
                            @endif
                        </td>
                        <td>{{ $mv->qty_in ?: '-' }}</td>
                        <td>{{ $mv->qty_out ?: '-' }}</td>
                        <td><strong>{{ $mv->balance_after }}</strong></td>
                        <td>{{ ucwords(str_replace('_',' ',$mv->reference_type)) }} #{{ $mv->reference_id }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No stock movements.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
