@extends('layouts.app')
@section('page-title', 'Create Supplier')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<x-card title="New Supplier">
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        @include('suppliers._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save</x-button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
