@extends('layouts.app')
@section('page-title', 'Approval Settings')
@section('breadcrumb')<li class="breadcrumb-item active">Approval Settings</li>@endsection
@section('content')
<div class="erp-card mb-3">
    <div class="erp-card-header"><div class="section-title"><i class="bi bi-plus-circle me-2"></i>Add Approval Level</div></div>
    <div class="erp-card-body">
        <form action="{{ route('approval-settings.store') }}" method="POST">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label" style="font-size:.8rem;">Document Type</label>
                    <select name="document_type" class="form-select form-select-sm" required style="border-radius:10px;">
                        <option value="pr">Purchase Request</option>
                        <option value="po">Purchase Order</option>
                        <option value="wo">Work Order</option>
                        <option value="gi">Goods Issue</option>
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">Level Name</label><input type="text" name="level_name" class="form-control form-control-sm" required placeholder="Foreman, Manager..." style="border-radius:10px;"></div>
                <div class="col-md-1"><label class="form-label" style="font-size:.8rem;">Order</label><input type="number" name="level_order" class="form-control form-control-sm" value="1" min="1" required style="border-radius:10px;"></div>
                <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">Min Budget</label><input type="number" name="min_budget" class="form-control form-control-sm" value="0" min="0" style="border-radius:10px;"></div>
                <div class="col-md-2"><label class="form-label" style="font-size:.8rem;">Max Budget</label><input type="number" name="max_budget" class="form-control form-control-sm" placeholder="Unlimited" style="border-radius:10px;"></div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:.8rem;">Approver</label>
                    <select name="approver_user_id" class="form-select form-select-sm" style="border-radius:10px;">
                        <option value="">-- By Role --</option>
                        @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>@endforeach
                    </select>
                </div>
                <div class="col-auto"><button class="btn btn-danger btn-sm" style="border-radius:10px;"><i class="bi bi-plus-lg me-1"></i>Add</button></div>
            </div>
        </form>
    </div>
</div>
<div class="erp-card">
    <div class="erp-card-header d-flex justify-content-between align-items-center">
        <div class="section-title">Configured Levels</div>
        <form method="GET" class="d-flex gap-2">
            <select name="document_type" class="form-select form-select-sm" style="border-radius:10px;width:auto;">
                <option value="">All</option>
                @foreach(['pr'=>'PR','po'=>'PO','wo'=>'WO','gi'=>'GI'] as $k=>$v)<option value="{{ $k }}" {{ request('document_type')==$k?'selected':'' }}>{{ $v }}</option>@endforeach
            </select>
            <button class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">Filter</button>
        </form>
    </div>
    <div class="erp-card-body">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Type</th><th>Order</th><th>Level</th><th>Min Budget</th><th>Max Budget</th><th>Approver</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($settings as $s)
                    <tr>
                        <td><span class="badge badge-soft-info" style="border-radius:999px;">{{ strtoupper($s->document_type) }}</span></td>
                        <td>{{ $s->level_order }}</td>
                        <td><strong>{{ $s->level_name }}</strong></td>
                        <td>{{ number_format($s->min_budget, 0, ',', '.') }}</td>
                        <td>{{ $s->max_budget ? number_format($s->max_budget, 0, ',', '.') : 'Unlimited' }}</td>
                        <td>{{ $s->approverUser->name ?? ($s->approver_role ?? 'Any') }}</td>
                        <td>@include('components.status-badge', ['status' => $s->is_active ? 'available' : 'standby'])</td>
                        <td><form action="{{ route('approval-settings.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" style="border-radius:8px;"><i class="bi bi-trash"></i></button></form></td>
                    </tr>
                    @empty<tr><td colspan="8" class="text-center text-muted py-4">No levels configured.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $settings->links() }}</div>
    </div>
</div>
@endsection
