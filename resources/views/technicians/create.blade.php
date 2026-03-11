@extends('layouts.app')
@section('page-title', 'Create Technician')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('technicians.index') }}">Technicians</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<div class="erp-card"><div class="erp-card-header"><div class="section-title">New Technician</div></div><div class="erp-card-body">
<form action="{{ route('technicians.store') }}" method="POST">@csrf @include('technicians._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Save</button> <a href="{{ route('technicians.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
@endsection
