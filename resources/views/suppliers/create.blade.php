@extends('layouts.app')
@section('page-title', 'Create Supplier')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<div class="erp-card"><div class="erp-card-header"><div class="section-title">New Supplier</div></div><div class="erp-card-body">
<form action="{{ route('suppliers.store') }}" method="POST">@csrf @include('suppliers._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Save</button> <a href="{{ route('suppliers.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
@endsection
