@extends('print.layout')
@section('doc-title', 'BA Kerusakan Ban - ' . $tireDamageReport->report_no)
@section('content')
<div class="print-header">
    <div>
        <div class="company-name">APEX</div>
        <div class="company-sub">PT Anugerah Sarana Hikmah</div>
    </div>
    <div>
        <div class="doc-title">BERITA ACARA KERUSAKAN BAN</div>
        <div class="doc-number">{{ $tireDamageReport->report_no }}</div>
    </div>
</div>

<table class="info-table">
    <tr>
        <td class="label">Tanggal BA</td>
        <td>{{ $tireDamageReport->report_date->format('d M Y') }}</td>
        <td class="label">Status</td>
        <td>{{ $tireDamageReport->status === 'approved' ? 'DISETUJUI' : 'DRAFT' }}</td>
    </tr>
    <tr>
        <td class="label">Unit</td>
        <td>{{ $tireDamageReport->unit->unit_code }} — {{ $tireDamageReport->unit->unit_model }}</td>
        <td class="label">Posisi Ban</td>
        <td>{{ $tireDamageReport->unitTire->position_label ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Nama Ban</td>
        <td>{{ $tireDamageReport->unitTire->sparepart->part_name ?? '-' }}</td>
        <td class="label">No. Seri Ban</td>
        <td>{{ $tireDamageReport->unitTire->serial_number ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Pasang</td>
        <td>{{ $tireDamageReport->installed_at?->format('d M Y') ?? '-' }}</td>
        <td class="label">KM Batas</td>
        <td>{{ number_format($tireDamageReport->unitTire->km_limit, 0, ',', '.') }} km</td>
    </tr>
    <tr>
        <td class="label">Odometer Saat Rusak</td>
        <td>{{ number_format($tireDamageReport->km_at_damage, 0, ',', '.') }} km</td>
        <td class="label">KM Terpakai saat Rusak</td>
        <td><strong>{{ number_format($tireDamageReport->km_used_when_damaged, 0, ',', '.') }} km</strong></td>
    </tr>
    <tr>
        <td class="label">Jenis Kerusakan</td>
        <td>{{ \App\Models\TireDamageReport::damageTypeLabel($tireDamageReport->damage_type) }}</td>
        <td class="label">Klaim Garansi</td>
        <td>{{ $tireDamageReport->is_warranty_claim ? 'YA' : 'TIDAK' }}</td>
    </tr>
</table>

<div style="margin:16px 0;padding:12px;border:1px solid #ddd;border-radius:4px;">
    <div style="font-size:11px;color:#555;margin-bottom:6px;font-weight:600;">DESKRIPSI KERUSAKAN:</div>
    <div style="font-size:12px;line-height:1.6;">{{ $tireDamageReport->damage_description }}</div>
    @if($tireDamageReport->notes)
    <div style="font-size:11px;color:#555;margin-top:8px;font-weight:600;">CATATAN:</div>
    <div style="font-size:12px;">{{ $tireDamageReport->notes }}</div>
    @endif
</div>

<div style="margin-top:40px;">
    <table width="100%">
        <tr>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Dibuat oleh</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">( ________________________ )</div>
            </td>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Mengetahui</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">( ________________________ )</div>
            </td>
            <td width="33%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">Menyetujui</div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">
                    @if($tireDamageReport->approved_by)
                    {{ $tireDamageReport->approved_by }}
                    @else
                    ( ________________________ )
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>
@endsection
