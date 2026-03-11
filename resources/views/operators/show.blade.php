@extends('layouts.app')
@section('page-title', 'Operator Detail')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('operators.index') }}">Operators</a></li><li class="breadcrumb-item active">{{ $operator->operator_code }}</li>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="erp-card p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;width:56px;height:56px;font-size:1.4rem;border-radius:16px;"><i class="bi bi-person-badge"></i></div>
                <div><div style="font-weight:800;font-size:1.2rem;">{{ $operator->operator_code }}</div><div class="text-muted">{{ $operator->operator_name }}</div></div>
            </div>
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">NIK</td><td>{{ $operator->nik ?? '-' }}</td></tr>
                <tr><td class="text-muted">Phone</td><td>{{ $operator->phone ?? '-' }}</td></tr>
                <tr><td class="text-muted">License</td><td>{{ $operator->license_type ?? '-' }}</td></tr>
                <tr><td class="text-muted">Expiry</td><td>
                    @if($operator->license_expiry)
                        {{ $operator->license_expiry->format('d M Y') }}
                        @if($operator->isLicenseExpired()) <span class="badge badge-soft-danger" style="border-radius:999px;">Expired</span> @elseif($operator->isLicenseExpiringSoon()) <span class="badge badge-soft-warning" style="border-radius:999px;">Expiring Soon</span> @endif
                    @else - @endif
                </td></tr>
            </table>
            <div class="mt-3"><a href="{{ route('operators.edit', $operator) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-pencil me-1"></i>Edit</a></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="erp-card"><div class="erp-card-header"><div class="section-title">P2H History</div></div><div class="erp-card-body">
            <div class="table-responsive"><table class="table table-modern mb-0"><thead><tr><th>P2H No</th><th>Unit</th><th>Date</th><th>Shift</th><th>Status</th></tr></thead><tbody>
            @forelse($operator->p2hChecks as $p)
            <tr>
                <td><a href="{{ route('p2h.show', $p) }}">{{ $p->p2h_number }}</a></td>
                <td>{{ $p->unit->unit_code ?? '-' }}</td>
                <td>{{ $p->check_date->format('d M Y') }}</td>
                <td>{{ ucfirst($p->shift) }}</td>
                <td>@include('components.p2h-status', ['status' => $p->overall_status])</td>
            </tr>
            @empty<tr><td colspan="5" class="text-center text-muted py-3">No P2H records.</td></tr>@endforelse
            </tbody></table></div>
        </div></div>
    </div>
</div>
@endsection
