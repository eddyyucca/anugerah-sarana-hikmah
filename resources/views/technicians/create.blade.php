@extends('layouts.app')
@section('page-title', 'Create Technician')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('technicians.index') }}">Technicians</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<<<<<<< HEAD
<x-card title="New Technician">
    <form action="{{ route('technicians.store') }}" method="POST">
        @csrf
        @include('technicians._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save</x-button>
            <a href="{{ route('technicians.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
=======
<div class="erp-card"><div class="erp-card-header"><div class="section-title">New Technician</div></div><div class="erp-card-body">
<form action="{{ route('technicians.store') }}" method="POST">@csrf @include('technicians._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Save</button> <a href="{{ route('technicians.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
@endsection
