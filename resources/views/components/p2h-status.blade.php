@php
$colors = [
    'layak' => ['badge-soft-success', 'Layak'],
    'layak_catatan' => ['badge-soft-warning', 'Layak + Catatan'],
    'tidak_layak' => ['badge-soft-danger', 'Tidak Layak'],
];
$c = $colors[$status] ?? ['badge-soft-info', ucwords(str_replace('_', ' ', $status))];
@endphp
<span class="badge {{ $c[0] }}" style="border-radius:999px;padding:.35em .75em;">{{ $c[1] }}</span>
