@extends('layouts.app')
@section('page-title', 'Spareparts')
@section('breadcrumb')<li class="breadcrumb-item active">Spareparts</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Sparepart List</div>
        <a href="{{ route('spareparts.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;"><i class="bi bi-plus-lg me-1"></i> Add Sparepart</a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search part number / name..." value="{{ request('search') }}" style="border-radius:10px;"></div>
            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Category</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="stock_filter" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock_filter')=='low'?'selected':'' }}>Low Stock</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('spareparts.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Part Number</th><th>Name</th><th>Category</th><th>UOM</th><th>Price</th><th>Stock</th><th>Min</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($spareparts as $sp)
                    <tr>
                        <td><strong>{{ $sp->part_number }}</strong></td>
                        <td>{{ $sp->part_name }}</td>
                        <td>{{ $sp->category->name ?? '-' }}</td>
                        <td>{{ $sp->uom }}</td>
                        <td>{{ number_format($sp->unit_price, 0, ',', '.') }}</td>
                        <td>
                            {{ $sp->stock_on_hand }}
                            @if($sp->isLowStock()) <span class="badge badge-soft-danger" style="border-radius:999px;">Low</span> @endif
                        </td>
                        <td>{{ $sp->minimum_stock }}</td>
                        <td>
                            <a href="{{ route('spareparts.show', $sp) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('spareparts.edit', $sp) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('spareparts.destroy', $sp) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No spareparts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $spareparts->links() }}</div>
    </div>
</div>
@endsection
