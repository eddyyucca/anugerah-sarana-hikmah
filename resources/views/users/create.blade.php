@extends('layouts.app')
@section('page-title', 'Tambah Akun')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Akun</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('users.index') }}" class="topbar-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-bold">Tambah Akun Pengguna</h5>
                <div class="text-muted" style="font-size:.83rem;">Buat akun baru untuk mengakses sistem</div>
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
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                @include('users._form')

                <div class="d-flex gap-2 mt-4 pt-4 border-top flex-wrap">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-person-plus me-1"></i> Buat Akun
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection