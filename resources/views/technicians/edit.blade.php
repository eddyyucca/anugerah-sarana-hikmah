@extends('layouts.app')
@section('page-title', 'Edit Technician')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('technicians.index') }}">Technicians</a></li><li class="breadcrumb-item active">Edit</li>@endsection
@section('content')
<x-card title="Edit: {{ $technician->technician_code }}">
    <form action="{{ route('technicians.update', $technician) }}" method="POST">
        @csrf
        @method('PUT')
        @include('technicians._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Update</x-button>
            <a href="{{ route('technicians.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
