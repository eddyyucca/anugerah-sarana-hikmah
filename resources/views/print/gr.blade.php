@extends('print.layout')
@section('doc-title', 'Goods Receipt - ' . $goodsReceipt->gr_number)
@section('content')
<div class="print-header">
    <div><div class="company-name">Workshop ERP</div><div class="company-sub">Mining Logistics System</div></div>
    <div><div class="doc-title">GOODS RECEIPT</div><div class="doc-number">{{ $goodsReceipt->gr_number }}</div></div>
</div>
<table class="info-table">
    <tr><td class="label">Receipt Date</td><td>{{ $goodsReceipt->receipt_date->format('d M Y') }}</td><td class="label">PO Reference</td><td>{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}</td></tr>
    <tr><td class="label">Supplier</td><td>{{ $goodsReceipt->purchaseOrder->supplier->supplier_name ?? '-' }}</td><td class="label">Posted By</td><td>{{ $goodsReceipt->postedByUser->name ?? '-' }}</td></tr>
</table>
<table class="items-table">
    <thead><tr><th>No</th><th>Part Number</th><th>Description</th><th>Location</th><th style="text-align:right;">Qty Received</th></tr></thead>
    <tbody>
        @foreach($goodsReceipt->items as $i => $item)
        <tr><td>{{ $i+1 }}</td><td><strong>{{ $item->sparepart->part_number }}</strong></td><td>{{ $item->sparepart->part_name }}</td><td>{{ $item->warehouseLocation->name ?? '-' }}</td><td style="text-align:right;">{{ $item->qty_received }}</td></tr>
        @endforeach
    </tbody>
</table>
<div class="sign-area">
    <div class="sign-box"><div>Received By</div><div class="sign-line">Warehouse</div></div>
    <div class="sign-box"><div>Checked By</div><div class="sign-line">QC</div></div>
</div>
@endsection
