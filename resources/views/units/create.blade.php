@extends('layouts.app')
@section('page-title', 'Create Unit')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="erp-card">
    <div class="erp-card-header"><div class="section-title">New Unit</div></div>
    <div class="erp-card-body">
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            @include('units._form')
            <div class="mt-3">
                <button type="submit" class="btn btn-danger" style="border-radius:12px;">Save Unit</button>
                <a href="{{ route('units.index') }}" class="btn btn-light" style="border-radius:12px;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
