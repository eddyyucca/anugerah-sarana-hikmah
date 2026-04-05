@extends('layouts.app')
@section('page-title', 'Edit Sparepart')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Spareparts</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<x-card title="Edit: {{ $sparepart->part_number }}">
    <form action="{{ route('spareparts.update', $sparepart) }}" method="POST">
        @csrf
        @method('PUT')
        @include('spareparts._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Update</x-button>
            <a href="{{ route('spareparts.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
