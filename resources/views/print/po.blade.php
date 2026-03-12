@extends('print.layout')
@section('doc-title', 'Purchase Order - ' . $purchaseOrder->po_number)

@section('content')
<div class="print-header">
    <div>
        <div class="company-name">Workshop ERP</div>
        <div class="company-sub">Mining Logistics System</div>
        <div class="company-sub">Jl. Mining Site KM 12, Kalimantan Selatan</div>
    </div>
    <div>
        <div class="doc-title">PURCHASE ORDER</div>
        <div class="doc-number">{{ $purchaseOrder->po_number }}</div>
        <div class="doc-number">Status: <span class="badge badge-issued">{{ strtoupper($purchaseOrder->status) }}</span></div>
    </div>
</div>

<table class="info-table">
    <tr>
        <td class="label">Supplier</td>
        <td><strong>{{ $purchaseOrder->supplier->supplier_name ?? '-' }}</strong></td>
        <td class="label">PO Date</td>
        <td>{{ $purchaseOrder->po_date->format('d M Y') }}</td>
    </tr>
    <tr>
        <td class="label">Contact</td>
        <td>{{ $purchaseOrder->supplier->contact_person ?? '-' }}</td>
        <td class="label">Expected Date</td>
        <td>{{ $purchaseOrder->expected_date?->format('d M Y') ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Phone</td>
        <td>{{ $purchaseOrder->supplier->phone ?? '-' }}</td>
        <td class="label">PR Reference</td>
        <td>{{ $purchaseOrder->purchaseRequest->pr_number ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td>{{ $purchaseOrder->supplier->email ?? '-' }}</td>
        <td class="label">Remarks</td>
        <td>{{ $purchaseOrder->remarks ?? '-' }}</td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr><th>No</th><th>Part Number</th><th>Description</th><th>UOM</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Unit Price (IDR)</th><th style="text-align:right;">Total (IDR)</th></tr>
    </thead>
    <tbody>
        @foreach($purchaseOrder->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $item->sparepart->part_number }}</strong></td>
            <td>{{ $item->sparepart->part_name }}</td>
            <td>{{ $item->sparepart->uom }}</td>
            <td style="text-align:right;">{{ $item->qty }}</td>
            <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
            <td style="text-align:right;">{{ number_format($item->total_price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="6" style="text-align:right;"><strong>TOTAL</strong></td>
            <td style="text-align:right;"><strong>IDR {{ number_format($purchaseOrder->items->sum('total_price'), 0, ',', '.') }}</strong></td>
        </tr>
    </tfoot>
</table>

@if($purchaseOrder->remarks)
<div class="notes-box"><strong>Notes:</strong> {{ $purchaseOrder->remarks }}</div>
@endif

<div class="notes-box">
    <strong>Terms & Conditions:</strong><br>
    1. Delivery sesuai tanggal yang tertera.<br>
    2. Barang harus sesuai spesifikasi dan qty.<br>
    3. Invoice dikirim bersama barang.<br>
    4. Payment term: 30 hari setelah invoice diterima.
</div>

<div class="sign-area">
    <div class="sign-box">
        <div>Prepared By</div>
        <div class="sign-line">Purchasing</div>
    </div>
    <div class="sign-box">
        <div>Approved By</div>
        <div class="sign-line">Manager</div>
    </div>
    <div class="sign-box">
        <div>Received By</div>
        <div class="sign-line">Supplier</div>
    </div>
</div>
@endsection
