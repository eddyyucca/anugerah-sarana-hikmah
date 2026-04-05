@extends('layouts.app')
@section('page-title', 'Create User Account')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <x-card title="Create New User Account" icon="bi bi-person-plus">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><strong>Error!</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                @include('users._form')

                <div class="mt-5 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-danger" style="border-radius:8px;">
                        <i class="bi bi-check-lg me-1"></i>Create User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                        <i class="bi bi-arrow-left me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
