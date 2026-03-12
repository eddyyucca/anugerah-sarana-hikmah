@extends('print.layout')
@section('doc-title', 'Work Order - ' . $workOrder->wo_number)
@section('content')
<div class="print-header">
    <div><div class="company-name">Workshop ERP</div><div class="company-sub">Mining Logistics System</div></div>
    <div><div class="doc-title">WORK ORDER</div><div class="doc-number">{{ $workOrder->wo_number }}</div></div>
</div>
<table class="info-table">
    <tr><td class="label">Unit</td><td><strong>{{ $workOrder->unit->unit_code }}</strong> - {{ $workOrder->unit->unit_model }}</td><td class="label">Type</td><td>{{ ucfirst($workOrder->maintenance_type) }}</td></tr>
    <tr><td class="label">Technician</td><td>{{ $workOrder->technician->technician_name ?? '-' }}</td><td class="label">Status</td><td>{{ ucwords(str_replace('_',' ',$workOrder->status)) }}</td></tr>
    <tr><td class="label">Start</td><td>{{ $workOrder->start_time?->format('d M Y H:i') ?? '-' }}</td><td class="label">End</td><td>{{ $workOrder->end_time?->format('d M Y H:i') ?? '-' }}</td></tr>
    <tr><td class="label">Downtime</td><td>{{ $workOrder->downtime_hours }} hours</td><td class="label"></td><td></td></tr>
</table>
<div class="notes-box"><strong>Complaint:</strong> {{ $workOrder->complaint ?? '-' }}</div>
@if($workOrder->action_taken)<div class="notes-box"><strong>Action Taken:</strong> {{ $workOrder->action_taken }}</div>@endif
@if($workOrder->goodsIssues->count() > 0)
<div style="margin-bottom:10px;font-weight:700;">Parts Used:</div>
<table class="items-table">
    <thead><tr><th>Part Number</th><th>Name</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Cost (IDR)</th></tr></thead>
    <tbody>
        @foreach($workOrder->goodsIssues as $gi)@foreach($gi->items as $item)
        <tr><td>{{ $item->sparepart->part_number }}</td><td>{{ $item->sparepart->part_name }}</td><td style="text-align:right;">{{ $item->qty_issued }}</td><td style="text-align:right;">{{ number_format($item->total_price, 0, ',', '.') }}</td></tr>
        @endforeach @endforeach
    </tbody>
</table>
@endif
<div style="margin-bottom:10px;font-weight:700;">Cost Summary:</div>
<table class="info-table" style="width:300px;">
    @php $cs = $workOrder->costSummary; @endphp
    <tr><td class="label">Sparepart</td><td style="text-align:right;">IDR {{ number_format($cs->sparepart_cost ?? 0, 0, ',', '.') }}</td></tr>
    <tr><td class="label">Labor</td><td style="text-align:right;">IDR {{ number_format($workOrder->labor_cost, 0, ',', '.') }}</td></tr>
    <tr><td class="label">Vendor</td><td style="text-align:right;">IDR {{ number_format($workOrder->vendor_cost, 0, ',', '.') }}</td></tr>
    <tr><td class="label">Consumable</td><td style="text-align:right;">IDR {{ number_format($workOrder->consumable_cost, 0, ',', '.') }}</td></tr>
    <tr style="border-top:2px solid #111;"><td class="label"><strong>TOTAL</strong></td><td style="text-align:right;"><strong>IDR {{ number_format(($cs->total_cost ?? 0), 0, ',', '.') }}</strong></td></tr>
</table>
<div class="sign-area">
    <div class="sign-box"><div>Technician</div><div class="sign-line">{{ $workOrder->technician->technician_name ?? '______' }}</div></div>
    <div class="sign-box"><div>Supervisor</div><div class="sign-line">______</div></div>
</div>
@endsection
