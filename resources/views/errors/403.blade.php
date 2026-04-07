@extends('errors.layout')

@section('badge', 'Akses Ditolak')
@section('icon') <i class="bi bi-shield-lock-fill" style="color:#f59e0b;"></i> @endsection
@section('code', '403')
@section('title', 'Akses Ditolak')
@section('description')
    Kamu tidak memiliki izin untuk mengakses halaman ini.<br>
    Hubungi administrator jika kamu merasa ini adalah kesalahan.
@endsection

@section('gradient-start', '#f59e0b')
@section('gradient-end', '#ef4444')
@section('blob1', '#f59e0b')
@section('blob2', '#ef4444')
@section('blob3', '#f97316')
@section('badge-bg', 'rgba(245,158,11,.1)')
@section('badge-color', '#d97706')
