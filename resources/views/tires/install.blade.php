@extends('layouts.app')
@section('page-title', 'Pasang Ban')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('units.show', $unit) }}">{{ $unit->unit_code }}</a></li>
    <li class="breadcrumb-item active">Pasang Ban</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-plus-circle me-2 text-danger"></i>Pasang Ban ke {{ $unit->unit_code }}</div>
        <small class="text-muted">ODO unit saat ini: <strong>{{ number_format($unit->current_odometer, 0, ',', '.') }} km</strong></small>
    </div>
    <div class="erp-card-body">
        @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif

        <form action="{{ route('tires.install', $unit) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Posisi Roda <span class="text-danger">*</span></label>
                <select name="position_number" class="form-select @error('position_number') is-invalid @enderror" required>
                    <option value="">-- Pilih Posisi --</option>
                    @foreach($positions as $num => $label)
                    <option value="{{ $num }}"
                        {{ old('position_number', request('pos')) == $num ? 'selected' : '' }}>
                        #{{ $num }} — {{ $label }}
                        @if(isset($installed[$num]))
                            (Terisi: {{ $installed[$num]->sparepart->part_name ?? '-' }})
                        @endif
                    </option>
                    @endforeach
                </select>
                @error('position_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ban dari Inventory <span class="text-danger">*</span></label>
                <select name="sparepart_id" class="form-select @error('sparepart_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Sparepart Ban --</option>
                    @forelse($spareparts as $sp)
                    <option value="{{ $sp->id }}" {{ old('sparepart_id') == $sp->id ? 'selected' : '' }}>
                        {{ $sp->part_name }}
                        @if($sp->part_number) ({{ $sp->part_number }}) @endif
                        — Stok: {{ $sp->stock_on_hand }} {{ $sp->uom }}
                    </option>
                    @empty
                    @endforelse
                </select>
                @if($spareparts->isEmpty())
                <div class="alert alert-warning py-2 mt-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Tidak ada sparepart ban di inventory. Nama sparepart harus mengandung kata "ban", "tire", atau "tyre".
                </div>
                @endif
                @error('sparepart_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nomor Seri Ban</label>
                <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                    value="{{ old('serial_number') }}"
                    placeholder="Kosong jika belum diisi saat GR"
                    list="serial_suggestions">
                <datalist id="serial_suggestions">
                    {{-- Jika diisi dari GR, akan muncul sebagai suggestion --}}
                </datalist>
                @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Nomor seri sebaiknya diinput saat <strong>Goods Receipt (GR)</strong> diterima.
                    Jika sudah diinput saat GR, pilih ban dari inventory yang sudah memiliki nomor seri.
                </small>
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Batas KM <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="km_limit" class="form-control @error('km_limit') is-invalid @enderror"
                            value="{{ old('km_limit', 40000) }}" min="1000" step="1000" required>
                        <span class="input-group-text">km</span>
                    </div>
                    @error('km_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Pasang <span class="text-danger">*</span></label>
                    <input type="date" name="installed_at" class="form-control" value="{{ old('installed_at', date('Y-m-d')) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Opsional">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-danger" style="border-radius:10px;">
                    <i class="bi bi-check-lg me-1"></i>Pasang Ban
                </button>
                <a href="{{ route('units.show', $unit) }}" class="btn btn-outline-secondary" style="border-radius:10px;">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
