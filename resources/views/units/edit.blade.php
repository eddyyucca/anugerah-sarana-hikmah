@extends('layouts.app')
@section('page-title', 'Edit Unit')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<x-card title="Edit Unit: {{ $unit->unit_code }}">
    <form action="{{ route('units.update', $unit) }}" method="POST">
        @csrf @method('PUT')
        @include('units._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Update Unit</x-button>
            <a href="{{ route('units.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
