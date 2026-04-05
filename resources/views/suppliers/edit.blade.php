@extends('layouts.app')
@section('page-title', 'Edit Supplier')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li><li class="breadcrumb-item active">Edit</li>@endsection
@section('content')
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
@endsection
