@extends('layouts.app')
@section('page-title', 'Create Operator')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('operators.index') }}">Operators</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<<<<<<< HEAD
<x-card title="New Operator">
    <form action="{{ route('operators.store') }}" method="POST">
        @csrf
        @include('operators._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save</x-button>
            <a href="{{ route('operators.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
=======
<div class="erp-card"><div class="erp-card-header"><div class="section-title">New Operator</div></div><div class="erp-card-body">
<form action="{{ route('operators.store') }}" method="POST">@csrf @include('operators._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Save</button> <a href="{{ route('operators.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
@endsection
