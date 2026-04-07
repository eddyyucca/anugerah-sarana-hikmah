@extends('errors.layout')

@section('badge', 'Server Error')
@section('icon') <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i> @endsection
@section('code', '500')
@section('title', 'Terjadi Kesalahan Server')
@section('description')
    Server mengalami masalah internal dan tidak bisa memproses permintaanmu.<br>
    Tim kami sudah diberitahu. Coba lagi beberapa saat.
@endsection

@section('gradient-start', '#ef4444')
@section('gradient-end', '#dc2626')
@section('blob1', '#ef4444')
@section('blob2', '#b91c1c')
@section('blob3', '#f97316')
@section('badge-bg', 'rgba(239,68,68,.1)')
@section('badge-color', '#dc2626')
