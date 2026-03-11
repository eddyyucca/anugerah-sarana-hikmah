@extends('layouts.app')
@section('page-title', 'Edit Unit')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title">Edit Unit: {{ $unit->unit_code }}</div></div>
    <div class="erp-card-body">
        <form action="{{ route('units.update', $unit) }}" method="POST">
            @csrf @method('PUT')
            @include('units._form')
            <div class="mt-3">
                <button type="submit" class="btn btn-danger" style="border-radius:12px;">Update Unit</button>
                <a href="{{ route('units.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
