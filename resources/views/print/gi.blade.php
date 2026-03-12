@extends('print.layout')
@section('doc-title', 'Goods Issue - ' . $goodsIssue->gi_number)
@section('content')
<div class="print-header">
    <div><div class="company-name">Workshop ERP</div><div class="company-sub">Mining Logistics System</div></div>
    <div><div class="doc-title">GOODS ISSUE</div><div class="doc-number">{{ $goodsIssue->gi_number }}</div></div>
</div>
<table class="info-table">
    <tr><td class="label">Issue Date</td><td>{{ $goodsIssue->issue_date->format('d M Y') }}</td><td class="label">WO Ref</td><td>{{ $goodsIssue->workOrder->wo_number ?? '-' }}</td></tr>
    @if($goodsIssue->workOrder)<tr><td class="label">Unit</td><td>{{ $goodsIssue->workOrder->unit->unit_code ?? '-' }}</td><td class="label">Posted By</td><td>{{ $goodsIssue->postedByUser->name ?? '-' }}</td></tr>@endif
</table>
<table class="items-table">
    <thead><tr><th>No</th><th>Part Number</th><th>Description</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Price (IDR)</th><th style="text-align:right;">Total (IDR)</th></tr></thead>
    <tbody>
        @foreach($goodsIssue->items as $i => $item)
        <tr><td>{{ $i+1 }}</td><td><strong>{{ $item->sparepart->part_number }}</strong></td><td>{{ $item->sparepart->part_name }}</td><td style="text-align:right;">{{ $item->qty_issued }}</td><td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td><td style="text-align:right;">{{ number_format($item->total_price, 0, ',', '.') }}</td></tr>
        @endforeach
    </tbody>
    <tfoot><tr class="total-row"><td colspan="5" style="text-align:right;"><strong>TOTAL</strong></td><td style="text-align:right;"><strong>IDR {{ number_format($goodsIssue->items->sum('total_price'), 0, ',', '.') }}</strong></td></tr></tfoot>
</table>
<div class="sign-area">
    <div class="sign-box"><div>Issued By</div><div class="sign-line">Warehouse</div></div>
    <div class="sign-box"><div>Received By</div><div class="sign-line">Technician</div></div>
</div>
@endsection
