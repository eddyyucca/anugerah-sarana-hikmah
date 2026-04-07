@extends('errors.layout')

@section('badge', 'Maintenance')
@section('icon') <i class="bi bi-tools" style="color:#8b5cf6;"></i> @endsection
@section('code', '503')
@section('title', 'Sedang Maintenance')
@section('description')
    Aplikasi sedang dalam pemeliharaan untuk meningkatkan layanan.<br>
    Kami akan segera kembali. Terima kasih atas kesabaran kamu.
@endsection

@section('gradient-start', '#8b5cf6')
@section('gradient-end', '#6d28d9')
@section('blob1', '#8b5cf6')
@section('blob2', '#6d28d9')
@section('blob3', '#a78bfa')
@section('badge-bg', 'rgba(139,92,246,.1)')
@section('badge-color', '#7c3aed')

@section('extra')
<p style="font-size:.85rem;color:#64748b;text-align:center;">
    <i class="bi bi-clock me-1"></i>
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Silakan coba kembali nanti.' }}
</p>
@endsection

@push('styles')
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    .error-icon i { display: inline-block; animation: spin 4s linear infinite; }
</style>
@endpush
