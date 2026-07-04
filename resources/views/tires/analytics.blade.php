@extends('layouts.app')
@section('page-title', 'Analitik Lifetime Ban')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tires.index') }}">Ban</a></li>
    <li class="breadcrumb-item active">Analitik Lifetime</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-12">
        <div class="erp-card">
            <div class="erp-card-header d-flex justify-content-between align-items-center">
                <div class="section-title">
                    <i class="bi bi-graph-up me-2 text-primary"></i>Analitik Rata-Rata Lifetime Ban (KM)
                </div>
                <a href="{{ route('tires.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="erp-card-body">
                @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif

                <div class="alert alert-info py-2 mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Data berdasarkan riwayat pelepasan ban yang sudah selesai. Hanya ban dengan <strong>km_used &gt; 0</strong> yang dihitung.<br>
                    <strong>Formula Rekomendasi:</strong> <code>AVG &minus; STDDEV</code> (dibulatkan ke ratusan terdekat).
                    Anda dapat mengubah nilai KM Limit sebelum menerapkan.
                </div>

                @if($stats->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bar-chart" style="font-size:3rem;"></i>
                    <div class="mt-2">Belum ada data riwayat pelepasan ban yang tersedia.</div>
                    <small>Data akan muncul setelah ban dilepas dari unit dengan alasan pemakaian.</small>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nama Ban</th>
                                <th>Part Number</th>
                                <th class="text-center">Sampel</th>
                                <th class="text-end">Avg KM</th>
                                <th class="text-end">Min KM</th>
                                <th class="text-end">Max KM</th>
                                <th class="text-end text-primary">Rekomendasi</th>
                                <th class="text-center">Ban Aktif</th>
                                <th style="min-width:200px;">Terapkan KM Limit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats as $row)
                            @php
                                $reliability = $row->sample_count >= 5 ? 'success' : ($row->sample_count >= 2 ? 'warning' : 'secondary');
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $row->part_name }}</strong>
                                </td>
                                <td class="text-muted">{{ $row->part_number ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $reliability }}">{{ $row->sample_count }}</span>
                                    @if($row->sample_count < 3)
                                    <small class="d-block text-muted" style="font-size:.7rem;">data kurang</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($row->avg_km, 0, ',', '.') }}</strong>
                                    <small class="text-muted d-block" style="font-size:.78rem;">±{{ number_format($row->stddev_km, 0, ',', '.') }}</small>
                                </td>
                                <td class="text-end text-muted">{{ number_format($row->min_km, 0, ',', '.') }}</td>
                                <td class="text-end text-muted">{{ number_format($row->max_km, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ number_format($row->recommended_km_limit, 0, ',', '.') }} km</strong>
                                </td>
                                <td class="text-center">
                                    @if($row->active_tires_count > 0)
                                        <span class="badge bg-success" title="Ban terpasang di unit">
                                            <i class="bi bi-check-circle me-1"></i>{{ $row->active_tires_count }} ban
                                        </span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('tires.set-km-limit') }}" method="POST" class="d-flex align-items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="sparepart_id" value="{{ $row->sparepart_id }}">
                                        <div class="input-group input-group-sm" style="max-width:160px;">
                                            <input type="number"
                                                name="km_limit"
                                                class="form-control form-control-sm text-center"
                                                value="{{ $row->recommended_km_limit }}"
                                                min="1000" step="500"
                                                title="Nilai default = rekomendasi. Ubah sesuai kebutuhan."
                                                style="border-radius:8px 0 0 8px; font-weight:600;">
                                            <span class="input-group-text" style="font-size:.75rem;">km</span>
                                        </div>
                                        <button type="submit" class="btn btn-xs btn-outline-primary"
                                            onclick="return confirm('Terapkan KM limit ini ke ban {{ $row->part_name }} yang masih baru (total_km < 1.000 km)?')"
                                            title="Terapkan ke ban baru yang belum banyak terpakai"
                                            style="white-space:nowrap;">
                                            <i class="bi bi-check-lg"></i> Terapkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3" style="font-size:.8rem;color:#6b7280;">
                    <i class="bi bi-lightbulb me-1 text-warning"></i>
                    <strong>Cara baca:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <li>Kolom <strong>Rekomendasi</strong> = AVG &minus; STDDEV (batas konservatif aman).</li>
                        <li>Ubah nilai di kolom <strong>Terapkan KM Limit</strong> jika ingin menetapkan batas custom.</li>
                        <li>Tombol <strong>Terapkan</strong> mengubah km_limit pada ban merek ini yang baru dipasang (total_km &lt; 1.000 km). Ban yang sudah banyak terpakai tidak akan terpengaruh.</li>
                        <li>Kolom <strong>Ban Aktif</strong> = jumlah ban merek ini yang sedang terpasang di unit.</li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
