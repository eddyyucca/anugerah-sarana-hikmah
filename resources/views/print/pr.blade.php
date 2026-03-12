@extends('print.layout')
@section('doc-title', 'Purchase Request - ' . $purchaseRequest->pr_number)
@section('content')
<div class="print-header">
    <div><div class="company-name">Workshop ERP</div><div class="company-sub">Mining Logistics System</div></div>
    <div><div class="doc-title">PURCHASE REQUEST</div><div class="doc-number">{{ $purchaseRequest->pr_number }}</div><div class="doc-number"><span class="badge badge-{{ $purchaseRequest->status == 'approved' ? 'approved' : 'issued' }}">{{ strtoupper($purchaseRequest->status) }}</span></div></div>
</div>
<table class="info-table">
    <tr><td class="label">Request Date</td><td>{{ $purchaseRequest->request_date->format('d M Y') }}</td><td class="label">Requester</td><td>{{ $purchaseRequest->requester->name ?? '-' }}</td></tr>
    <tr><td class="label">Status</td><td>{{ ucfirst($purchaseRequest->status) }}</td><td class="label">Approved By</td><td>{{ $purchaseRequest->approver->name ?? '-' }}</td></tr>
    @if($purchaseRequest->remarks)<tr><td class="label">Remarks</td><td colspan="3">{{ $purchaseRequest->remarks }}</td></tr>@endif
</table>
<table class="items-table">
    <thead><tr><th>No</th><th>Part Number</th><th>Description</th><th>UOM</th><th style="text-align:right;">Qty</th><th>Notes</th></tr></thead>
    <tbody>
        @foreach($purchaseRequest->items as $i => $item)
        <tr><td>{{ $i+1 }}</td><td><strong>{{ $item->sparepart->part_number }}</strong></td><td>{{ $item->sparepart->part_name }}</td><td>{{ $item->sparepart->uom }}</td><td style="text-align:right;">{{ $item->qty }}</td><td>{{ $item->notes ?? '-' }}</td></tr>
        @endforeach
    </tbody>
</table>
<div class="sign-area">
    <div class="sign-box"><div>Requested By</div><div class="sign-line">{{ $purchaseRequest->requester->name ?? '______' }}</div></div>
    <div class="sign-box"><div>Approved By</div><div class="sign-line">{{ $purchaseRequest->approver->name ?? '______' }}</div></div>
</div>
@endsection
