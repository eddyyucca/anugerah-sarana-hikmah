<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('doc-title', 'Print Document')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; padding: 20px; }
        .print-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #dc2626; padding-bottom: 12px; margin-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: 800; color: #111827; }
        .company-sub { font-size: 10px; color: #6b7280; }
        .doc-title { font-size: 16px; font-weight: 800; text-align: right; color: #dc2626; }
        .doc-number { font-size: 12px; color: #374151; text-align: right; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 8px; vertical-align: top; }
        .info-table .label { color: #6b7280; font-weight: 600; width: 120px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background: #1f2937; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
        .items-table tfoot td { border-top: 2px solid #111; font-weight: 700; }
        .items-table tr:nth-child(even) { background: #f9fafb; }
        .total-row { background: #fef2f2 !important; }
        .sign-area { display: flex; justify-content: space-between; margin-top: 40px; }
        .sign-box { text-align: center; width: 30%; }
        .sign-line { border-top: 1px solid #111; margin-top: 60px; padding-top: 4px; }
        .footer-print { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 9px; color: #9ca3af; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .badge-issued { background: #fef3c7; color: #b45309; }
        .badge-approved { background: #dcfce7; color: #15803d; }
        .notes-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:15px;text-align:right;">
        <button onclick="window.print()" style="background:#dc2626;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;">
            <span style="margin-right:4px;">&#128424;</span> Print / Save PDF
        </button>
        <button onclick="window.close()" style="background:#e5e7eb;color:#111;border:none;padding:8px 20px;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;margin-left:6px;">Close</button>
    </div>

    @yield('content')

    <div class="footer-print">
        Workshop ERP - Mining Logistics System &middot; Printed: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
