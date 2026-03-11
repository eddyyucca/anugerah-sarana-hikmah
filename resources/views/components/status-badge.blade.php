@php
$colors = [
    'draft' => 'badge-soft-info',
    'submitted' => 'badge-soft-warning',
    'approved' => 'badge-soft-success',
    'rejected' => 'badge-soft-danger',
    'closed' => 'badge-soft-info',
    'issued' => 'badge-soft-warning',
    'partial' => 'badge-soft-warning',
    'completed' => 'badge-soft-success',
    'cancelled' => 'badge-soft-danger',
    'posted' => 'badge-soft-success',
    'open' => 'badge-soft-info',
    'in_progress' => 'badge-soft-warning',
    'waiting_part' => 'badge-soft-warning',
    'available' => 'badge-soft-success',
    'under_repair' => 'badge-soft-danger',
    'standby' => 'badge-soft-warning',
];
$class = $colors[$status] ?? 'badge-soft-info';
@endphp
<span class="badge {{ $class }}" style="border-radius:999px;padding:.35em .75em;">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
