@extends('layouts.app')
@section('page-title', $p2h->p2h_number)
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('p2h.index') }}">P2H Check</a></li><li class="breadcrumb-item active">{{ $p2h->p2h_number }}</li>@endsection

@section('content')
<div class="row g-3">
    {{-- Left: Info --}}
    <div class="col-lg-4">
        <div class="erp-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div style="font-weight:800;font-size:1.2rem;">{{ $p2h->p2h_number }}</div>
                    <div class="text-muted" style="font-size:.85rem;">{{ $p2h->check_date->format('d M Y') }} &middot; {{ ucfirst($p2h->shift) }} Shift</div>
                </div>
                @include('components.p2h-status', ['status' => $p2h->overall_status])
            </div>
            <table class="table table-sm mb-3">
                <tr><td class="text-muted">Unit</td><td><a href="{{ route('units.show', $p2h->unit_id) }}"><strong>{{ $p2h->unit->unit_code }}</strong></a> - {{ $p2h->unit->unit_model }}</td></tr>
                <tr><td class="text-muted">Category</td><td>{{ $p2h->unit->category->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Operator</td><td><a href="{{ route('operators.show', $p2h->operator_id) }}">{{ $p2h->operator->operator_name }}</a></td></tr>
                <tr><td class="text-muted">Hour Meter</td><td>{{ number_format($p2h->hour_meter_start, 1) }}</td></tr>
                <tr><td class="text-muted">KM</td><td>{{ number_format($p2h->km_start, 1) }}</td></tr>
                @if($p2h->general_notes)<tr><td class="text-muted">Notes</td><td>{{ $p2h->general_notes }}</td></tr>@endif
                <tr><td class="text-muted">Reviewed</td><td>
                    @if($p2h->reviewed_at)
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $p2h->reviewer->name ?? 'System' }}</span>
                        <div class="text-muted" style="font-size:.75rem;">{{ $p2h->reviewed_at->format('d M Y H:i') }}</div>
                    @else
                        <span class="text-muted">Not yet reviewed</span>
                    @endif
                </td></tr>
            </table>

            @if(!$p2h->reviewed_at)
            <form action="{{ route('p2h.review', $p2h) }}" method="POST">@csrf
                <button class="btn btn-sm btn-success" style="border-radius:10px;"><i class="bi bi-check-lg me-1"></i>Mark as Reviewed</button>
            </form>
            @endif
        </div>

        {{-- Score Summary --}}
        <div class="erp-card p-3">
            <div class="section-title mb-3"><i class="bi bi-speedometer2 me-2"></i>Inspection Score</div>
            @php
                $total = $p2h->items->whereIn('condition', ['good','warning','bad'])->count();
                $good = $p2h->items->where('condition', 'good')->count();
                $warning = $p2h->items->where('condition', 'warning')->count();
                $bad = $p2h->items->where('condition', 'bad')->count();
                $na = $p2h->items->where('condition', 'na')->count();
                $score = $total > 0 ? round(($good / $total) * 100, 1) : 0;
            @endphp
            <div class="text-center mb-3">
                <div style="font-size:2.5rem;font-weight:800;color:{{ $score >= 80 ? '#10b981' : ($score >= 60 ? '#f59e0b' : '#ef4444') }};">{{ $score }}%</div>
                <div class="text-muted" style="font-size:.85rem;">Fitness Score</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span><i class="bi bi-check-circle text-success me-1"></i> Good</span>
                <strong class="text-success">{{ $good }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span><i class="bi bi-exclamation-triangle text-warning me-1"></i> Warning</span>
                <strong class="text-warning">{{ $warning }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span><i class="bi bi-x-circle text-danger me-1"></i> Bad</span>
                <strong class="text-danger">{{ $bad }}</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span><i class="bi bi-dash-circle text-secondary me-1"></i> N/A</span>
                <strong class="text-secondary">{{ $na }}</strong>
            </div>
        </div>
    </div>

    {{-- Right: Checklist Details --}}
    <div class="col-lg-8">
        @foreach($groupedItems as $category => $items)
        <div class="erp-card mb-3">
            <div class="erp-card-header">
                <div class="section-title">{{ $category }}</div>
            </div>
            <div class="erp-card-body p-0">
                <table class="table table-modern mb-0">
                    <thead><tr><th>Check Item</th><th style="width:120px;">Condition</th><th>Notes</th></tr></thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->check_item }}</td>
                            <td>
                                @if($item->condition === 'good')
                                    <span class="badge badge-soft-success" style="border-radius:999px;"><i class="bi bi-check-lg me-1"></i>Good</span>
                                @elseif($item->condition === 'warning')
                                    <span class="badge badge-soft-warning" style="border-radius:999px;"><i class="bi bi-exclamation-triangle me-1"></i>Warning</span>
                                @elseif($item->condition === 'bad')
                                    <span class="badge badge-soft-danger" style="border-radius:999px;"><i class="bi bi-x-lg me-1"></i>Bad</span>
                                @else
                                    <span class="badge badge-soft-info" style="border-radius:999px;">N/A</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
