@extends('layouts.app')
@section('page-title', 'Create Technician')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('technicians.index') }}">Technicians</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
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
@endsection
