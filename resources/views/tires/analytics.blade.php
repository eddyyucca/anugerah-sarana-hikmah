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
                    Data berdasarkan riwayat pelepasan ban yang sudah selesai. Hanya ban dengan <strong>km_used > 0</strong> yang dihitung.
                    Rekomendasi KM Limit = <code>AVG - STDDEV</code> (dibulatkan ke ratusan terdekat).
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
                                <th class="text-end text-primary">Rekomendasi KM Limit</th>
                                <th class="text-center">Terapkan</th>
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
                                    <form action="{{ route('tires.set-km-limit') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="sparepart_id" value="{{ $row->sparepart_id }}">
                                        <input type="hidden" name="km_limit" value="{{ $row->recommended_km_limit }}">
                                        <button type="submit" class="btn btn-xs btn-outline-primary"
                                            onclick="return confirm('Terapkan {{ number_format($row->recommended_km_limit, 0) }} km limit ke semua ban {{ $row->part_name }} yang belum banyak terpakai?')"
                                            title="Terapkan rekomendasi km limit ke ban yang baru dipasang">
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
                    <strong>Cara baca:</strong> Tombol "Terapkan" akan mengubah km_limit pada ban dengan merek/tipe ini yang baru dipasang (total_km &lt; 1.000 km).
                    Ban yang sudah banyak terpakai tidak akan terpengaruh.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
