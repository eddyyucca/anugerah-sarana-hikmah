@extends('layouts.app')
@section('page-title', 'Units')
@section('breadcrumb')<li class="breadcrumb-item active">Units</li>@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="section-title">Unit List</div>
        <a href="{{ route('units.create') }}" class="btn btn-danger btn-sm" style="border-radius:12px;">
            <i class="bi bi-plus-lg me-1"></i> Add Unit
        </a>
    </div>
    <div class="erp-card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code / model..." value="{{ request('search') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status')=='available'?'selected':'' }}>Available</option>
                    <option value="under_repair" {{ request('status')=='under_repair'?'selected':'' }}>Under Repair</option>
                    <option value="standby" {{ request('status')=='standby'?'selected':'' }}>Standby</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm" style="border-radius:10px;">
                    <option value="">All Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
                <a href="{{ route('units.index') }}" class="btn btn-light btn-sm" style="border-radius:10px;">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Unit Code</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Hour Meter</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td><strong>{{ $unit->unit_code }}</strong></td>
                        <td>{{ $unit->unit_model }}</td>
                        <td>{{ $unit->unit_type ?? '-' }}</td>
                        <td>{{ $unit->category->name ?? '-' }}</td>
                        <td>{{ $unit->department ?? '-' }}</td>
                        <td>@include('components.status-badge', ['status' => $unit->current_status])</td>
                        <td>{{ number_format($unit->hour_meter, 1) }}</td>
                        <td>
                            <a href="{{ route('units.show', $unit) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-light" style="border-radius:8px;"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No units found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $units->links() }}</div>
    </div>
</div>
@endsection
