@extends('layouts.app')
@section('page-title', 'Create Unit')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('units.index') }}">Units</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<x-card title="New Unit">
    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        @include('units._form')
        <div class="mt-3">
            <x-button type="submit" variant="primary">Save Unit</x-button>
            <a href="{{ route('units.index') }}" class="btn btn-light btn-primary-rounded ms-2">Cancel</a>
        </div>
    </form>
</x-card>
@endsection
