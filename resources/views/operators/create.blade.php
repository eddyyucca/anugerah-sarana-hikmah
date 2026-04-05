@extends('layouts.app')
@section('page-title', 'Create Operator')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('operators.index') }}">Operators</a></li><li class="breadcrumb-item active">Create</li>@endsection
@section('content')
<x-card title="New Operator">
    <form action="{{ route('operators.store') }}" method="POST">
        @csrf
        @include('operators._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save</x-button>
            <a href="{{ route('operators.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
