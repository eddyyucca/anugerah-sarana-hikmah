@extends('layouts.app')
@section('page-title', 'Edit User: ' . $user->name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <x-card title="Edit User: {{ $user->name }}" icon="bi bi-pencil-square">
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

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                @include('users._form')

                <div class="mt-5 pt-3 border-top d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-danger" style="border-radius:8px;">
                        <i class="bi bi-check-lg me-1"></i>Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                        <i class="bi bi-arrow-left me-1"></i>Cancel
                    </a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline ms-auto" onsubmit="return confirm('Delete this user permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" style="border-radius:8px;">
                            <i class="bi bi-trash me-1"></i>Delete User
                        </button>
                    </form>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
