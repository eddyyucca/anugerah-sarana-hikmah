@extends('layouts.app')
@section('page-title', 'Sparepart Detail')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Spareparts</a></li>
<li class="breadcrumb-item active">{{ $sparepart->part_number }}</li>
@endsection
@section('content')
<div class="erp-card p-3">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;width:56px;height:56px;font-size:1.4rem;border-radius:16px;"><i class="bi bi-gear"></i></div>
        <div><div style="font-weight:800;font-size:1.2rem;">{{ $sparepart->part_number }}</div><div class="text-muted">{{ $sparepart->part_name }}</div></div>
    </div>
    <table class="table table-sm">
        <tr><td class="text-muted">Category</td><td>{{ $sparepart->category->name ?? '-' }}</td></tr>
        <tr><td class="text-muted">UOM</td><td>{{ $sparepart->uom }}</td></tr>
        <tr><td class="text-muted">Unit Price</td><td>{{ number_format($sparepart->unit_price, 0, ',', '.') }}</td></tr>
        <tr><td class="text-muted">Stock</td><td>{{ $sparepart->stock_on_hand }} @if($sparepart->isLowStock())<span class="badge badge-soft-danger" style="border-radius:999px;">Low Stock</span>@endif</td></tr>
        <tr><td class="text-muted">Minimum Stock</td><td>{{ $sparepart->minimum_stock }}</td></tr>
    </table>
    <a href="{{ route('spareparts.edit', $sparepart) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>
@endsection
