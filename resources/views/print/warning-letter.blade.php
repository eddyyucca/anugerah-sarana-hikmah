@extends('print.layout')
@section('doc-title', 'Surat Peringatan - ' . $operatorWarningLetter->letter_no)
@section('content')
<div class="print-header">
    <div>
        <div class="company-name">APEX</div>
        <div class="company-sub">PT Anugerah Sarana Hikmah</div>
    </div>
    <div>
        <div class="doc-title">SURAT PERINGATAN OPERATOR</div>
        <div class="doc-number">{{ $operatorWarningLetter->letter_no }}</div>
    </div>
</div>

<table class="info-table">
    <tr>
        <td class="label">Nomor Surat</td>
        <td>{{ $operatorWarningLetter->letter_no }}</td>
        <td class="label">Tanggal</td>
        <td>{{ $operatorWarningLetter->letter_date->format('d F Y') }}</td>
    </tr>
    <tr>
        <td class="label">Kepada</td>
        <td colspan="3">
            <strong>{{ $operatorWarningLetter->operator->operator_name ?? '-' }}</strong>
            ({{ $operatorWarningLetter->operator->operator_code ?? '-' }})
        </td>
    </tr>
    <tr>
        <td class="label">Unit</td>
        <td>{{ $operatorWarningLetter->unit->unit_code ?? '-' }} — {{ $operatorWarningLetter->unit->unit_model ?? '' }}</td>
        <td class="label">WO Pemicu</td>
        <td>{{ $operatorWarningLetter->workOrder->wo_number ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Periode</td>
        <td>{{ $operatorWarningLetter->year_month }}</td>
        <td class="label">Dibuat oleh</td>
        <td>{{ $operatorWarningLetter->created_by }}</td>
    </tr>
</table>

<div style="margin:16px 0;font-size:12px;line-height:1.8;">
    <p>Dengan hormat,</p>
    <p>
        Berdasarkan hasil evaluasi biaya perbaikan unit <strong>{{ $operatorWarningLetter->unit->unit_code ?? '-' }}</strong>
        pada periode <strong>{{ $operatorWarningLetter->year_month }}</strong>, kami menyampaikan surat peringatan ini kepada
        Saudara/i <strong>{{ $operatorWarningLetter->operator->operator_name ?? '-' }}</strong>.
    </p>
</div>

<div style="margin:16px 0;padding:12px;border:1px solid #ddd;border-radius:4px;">
    <div style="font-size:11px;color:#555;margin-bottom:6px;font-weight:600;">RINCIAN PELANGGARAN:</div>
    <table width="100%" style="font-size:12px;border-collapse:collapse;">
        <tr>
            <td style="padding:3px 0;width:40%;">Budget perbaikan ditetapkan</td>
            <td style="padding:3px 0;">: <strong>IDR {{ number_format($operatorWarningLetter->budget_limit, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:3px 0;">Total biaya perbaikan aktual</td>
            <td style="padding:3px 0;">: <strong>IDR {{ number_format($operatorWarningLetter->total_cost, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:3px 0;">Kelebihan biaya</td>
            <td style="padding:3px 0;">: <strong style="color:#c00;">IDR {{ number_format($operatorWarningLetter->excess_amount, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>

<div style="margin:16px 0;padding:12px;border:1px solid #ddd;border-radius:4px;">
    <div style="font-size:11px;color:#555;margin-bottom:6px;font-weight:600;">KETERANGAN:</div>
    <div style="font-size:12px;line-height:1.6;">{{ $operatorWarningLetter->violation_description }}</div>
</div>

<div style="margin:16px 0;font-size:12px;line-height:1.8;">
    <p>
        Kami mengharapkan perhatian dan tindakan perbaikan dari Saudara/i agar kejadian serupa tidak terulang kembali.
        Surat peringatan ini merupakan catatan resmi dalam penilaian kinerja operator.
    </p>
    <p>Demikian surat peringatan ini dibuat untuk diperhatikan dan ditindaklanjuti.</p>
</div>

<div style="margin-top:40px;">
    <table width="100%">
        <tr>
            <td width="45%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">
                    Yang menerima,<br>
                    <em>Operator</em>
                </div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">
                    {{ $operatorWarningLetter->operator->operator_name ?? '( __________________ )' }}
                </div>
                @if($operatorWarningLetter->acknowledged_at)
                <div style="font-size:10px;color:#555;margin-top:2px;">
                    Ditanda tangani: {{ $operatorWarningLetter->acknowledged_at->format('d M Y H:i') }}
                </div>
                @endif
            </td>
            <td width="10%"></td>
            <td width="45%" align="center">
                <div style="margin-bottom:50px;font-size:11px;">
                    Mengetahui,<br>
                    <em>Pimpinan / Atasan Langsung</em>
                </div>
                <div style="border-top:1px solid #333;padding-top:4px;font-size:11px;">
                    {{ $operatorWarningLetter->created_by ?? '( __________________ )' }}
                </div>
            </td>
        </tr>
    </table>
</div>
@endsection
