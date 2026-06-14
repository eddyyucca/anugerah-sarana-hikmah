@extends('print.layout')
@section('doc-title', 'Supplier Return - ' . $supplierReturn->return_no)
@section('content')
<div class="print-header">
    <div>
        <div class="company-name">APEX</div>
        <div class="company-sub">PT Anugerah Sarana Hikmah</div>
    </div>
    <div>
        <div class="doc-title">SUPPLIER RETURN</div>
        <div class="doc-number">{{ $supplierReturn->return_no }}</div>
    </div>
</div>
<table class="info-table">
    <tr>
        <td class="label">Tanggal Return</td>
        <td>{{ $supplierReturn->return_date->format('d M Y') }}</td>
        <td class="label">Status</td>
        <td>{{ $supplierReturn->status_label }}</td>
    </tr>
    <tr>
        <td class="label">Supplier</td>
        <td>{{ $supplierReturn->supplier->supplier_name }}</td>
        <td class="label">Ref GR</td>
        <td>{{ $supplierReturn->goodsReceipt->gr_number ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Alasan Return</td>
        <td colspan="3">{{ $supplierReturn->return_reason }}</td>
    </tr>
    @if($supplierReturn->confirmed_by)
    <tr>
        <td class="label">Dikonfirmasi</td>
        <td>{{ $supplierReturn->confirmed_by }}</td>
        <td class="label">Tanggal Konfirmasi</td>
        <td>{{ $supplierReturn->confirmed_at?->format('d M Y H:i') }}</td>
    </tr>
    @endif
</table>

<table class="items-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Sparepart</th>
            <th>Part Number</th>
            <th>Qty Return</th>
            <th>Alasan Cacat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($supplierReturn->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->sparepart->part_name }}</td>
            <td>{{ $item->sparepart->part_number ?? '-' }}</td>
            <td>{{ number_format($item->qty_returned, 2) }} {{ $item->sparepart->uom }}</td>
            <td>{{ $item->defect_reason }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:40px;">
    <table width="100%">
        <tr>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Dibuat oleh</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">( ________________________ )</div>
            </td>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Disetujui oleh</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">( ________________________ )</div>
            </td>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Diterima Supplier</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">( ________________________ )</div>
            </td>
        </tr>
    </table>
</div>
@endsection
