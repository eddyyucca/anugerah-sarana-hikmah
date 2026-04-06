@extends('layouts.app')
@section('page-title', 'Opname Stok Baru')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('stock-opname.index') }}">Stock Opname</a></li><li class="breadcrumb-item active">Baru</li>@endsection

@section('content')
@if($hasActive)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>Ada opname yang masih <strong>in progress</strong>. Selesaikan opname tersebut sebelum membuat yang baru.</span>
    <a href="{{ route('stock-opname.index', ['status' => 'in_progress']) }}" class="btn btn-sm btn-warning ms-auto">Lihat Opname Aktif</a>
</div>
@endif

<form action="{{ route('stock-opname.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-clipboard2-data me-2"></i>Buat Opname Menyeluruh</div>
        </x-slot:header>
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="Tanggal Opname" required>
                    <input type="date" name="opname_date" class="form-control @error('opname_date') is-invalid @enderror"
                        value="{{ old('opname_date', date('Y-m-d')) }}" required>
                    @error('opname_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-9">
                <x-form-group label="Keterangan">
                    <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Keterangan opsional...">
                </x-form-group>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2 mt-3 mb-0">
            <i class="bi bi-info-circle-fill mt-1"></i>
            <div>
                <strong>Opname Menyeluruh</strong> — Sistem akan memasukkan <strong>{{ $itemCount }} item</strong> aktif secara otomatis, diurutkan berdasarkan Bin Location.
                Petugas gudang dapat menyimpan progres dan melanjutkan penghitungan kapan saja.
            </div>
        </div>
    </x-card>

    <button type="submit" class="btn btn-danger" @if($hasActive) disabled @endif>
        <i class="bi bi-play-circle me-1"></i> Mulai Opname ({{ $itemCount }} item)
    </button>
    <a href="{{ route('stock-opname.index') }}" class="btn btn-light">Batal</a>
</form>
@endsection
