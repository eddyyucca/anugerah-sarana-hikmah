@extends('layouts.app')
@section('page-title', 'Edit Akun')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Akun</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('users.index') }}" class="topbar-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="flex-grow-1 min-w-0">
                <h5 class="mb-0 fw-bold text-truncate">Edit: {{ $user->name }}</h5>
                <div class="text-muted" style="font-size:.83rem;">Perbarui informasi akun pengguna</div>
            </div>
        </div>

        {{-- Error --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Ada kesalahan:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                <li style="font-size:.88rem;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="erp-card p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                @include('users._form')

                <div class="d-flex gap-2 mt-4 pt-4 border-top flex-wrap">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Hapus Akun (terpisah dari form utama) --}}
        @if(auth()->id() !== $user->id)
        <div class="erp-card p-4 mt-3 border-danger" style="border-color:#fca5a5 !important;">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <div class="fw-semibold text-danger mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Hapus Akun</div>
                    <div class="text-muted" style="font-size:.83rem;">Tindakan ini permanen dan tidak bisa dibatalkan.</div>
                </div>
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }} secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                        <i class="bi bi-trash me-1"></i> Hapus Akun
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection