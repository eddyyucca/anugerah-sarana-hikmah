@extends('layouts.app')
@section('page-title', 'Edit Supplier')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li><li class="breadcrumb-item active">Edit</li>@endsection
@section('content')
<<<<<<< HEAD
<x-card title="Edit: {{ $supplier->supplier_code }}">
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        @include('suppliers._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Update</x-button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
=======
<div class="erp-card"><div class="erp-card-header"><div class="section-title">Edit: {{ $supplier->supplier_code }}</div></div><div class="erp-card-body">
<form action="{{ route('suppliers.update', $supplier) }}" method="POST">@csrf @method('PUT') @include('suppliers._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Update</button> <a href="{{ route('suppliers.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
@endsection
