@extends('layouts.app')
@section('page-title', 'Edit Sparepart')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Spareparts</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="erp-card"><div class="erp-card-header"><div class="section-title">Edit: {{ $sparepart->part_number }}</div></div><div class="erp-card-body">
<form action="{{ route('spareparts.update', $sparepart) }}" method="POST">@csrf @method('PUT') @include('spareparts._form')
<div class="mt-3"><button type="submit" class="btn btn-danger" style="border-radius:12px;">Update</button> <a href="{{ route('spareparts.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a></div>
</form></div></div>
@endsection
