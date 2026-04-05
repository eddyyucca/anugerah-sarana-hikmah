@extends('layouts.app')
@section('page-title', 'Create Sparepart')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Spareparts</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection
@section('content')
<x-card title="New Sparepart">
    <form action="{{ route('spareparts.store') }}" method="POST">
        @csrf
        @include('spareparts._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save</x-button>
            <a href="{{ route('spareparts.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
