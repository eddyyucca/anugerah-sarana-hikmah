@extends('layouts.app')
@section('page-title', 'Edit Operator')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('operators.index') }}">Operators</a></li><li class="breadcrumb-item active">Edit</li>@endsection
@section('content')
<div class="erp-card"><div class="erp-card-header"><div class="section-title">Edit: {{ $operator->operator_code }}</div></div><div class="erp-card-body">
<form action="{{ route('operators.update', $operator) }}" method="POST">@csrf @method('PUT') @include('operators._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Update</button> <a href="{{ route('operators.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div></form></div></div>
@endsection
