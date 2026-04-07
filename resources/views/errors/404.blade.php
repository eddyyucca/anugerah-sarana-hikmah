@extends('errors.layout')

@section('badge', 'Halaman Tidak Ditemukan')
@section('icon') <i class="bi bi-compass" style="color:#3b82f6;"></i> @endsection
@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('description')
    Halaman yang kamu cari tidak ada atau sudah dipindahkan.<br>
    Coba periksa URL-nya atau kembali ke beranda.
@endsection

@section('gradient-start', '#3b82f6')
@section('gradient-end', '#6366f1')
@section('blob1', '#3b82f6')
@section('blob2', '#6366f1')
@section('blob3', '#06b6d4')
@section('badge-bg', 'rgba(59,130,246,.1)')
@section('badge-color', '#3b82f6')
