@extends('layouts.app')
@section('page-title', 'Menu & Role Settings')
@section('breadcrumb')<li class="breadcrumb-item active">Menu & Roles</li>@endsection

@section('content')
<<<<<<< HEAD
<div class="row">
    <div class="col-lg-12">
        {{-- Role Management Section --}}
        <div class="erp-card mb-4">
            <div class="erp-card-header">
                <div class="section-title">
                    <i class="bi bi-person-badge me-2"></i>Role Management
                </div>
            </div>
            <div class="erp-card-body">
                <div class="mb-3">
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle me-1"></i>Select a role to configure its menu permissions,
                        or add a new role to get started.
                    </p>
                </div>

                <div class="row g-3 align-items-end">
                    {{-- Role List --}}
                    <div class="col-lg-8">
                        <label class="form-label fw-bold mb-3">Available Roles</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($roles as $r)
                            <a href="{{ route('menu-settings.index', ['role' => $r]) }}"
                               class="btn {{ $currentRole === $r ? 'btn-danger' : 'btn-outline-secondary' }}"
                               style="border-radius:8px;padding:8px 16px;">
                                <i class="bi {{ $r === 'admin' ? 'bi-shield-check' : 'bi-person' }} me-1"></i>
                                <strong>{{ ucfirst($r) }}</strong>
                                @if($r === 'admin')
                                <span class="badge bg-warning text-dark ms-2">Full Access</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Add New Role --}}
                    <div class="col-lg-4">
                        <form action="{{ route('menu-settings.add-role') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="new_role" class="form-control"
                                   placeholder="New role name..."
                                   style="border-radius:8px;" required>
                            <button class="btn btn-outline-danger" style="border-radius:8px;white-space:nowrap;">
                                <i class="bi bi-plus-lg me-1"></i>Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permission Configuration Section --}}
        <div class="erp-card">
            <div class="erp-card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="section-title">
                            <i class="bi bi-sliders me-2"></i>Menu Permissions
                            <span class="badge bg-danger ms-2">{{ ucfirst($currentRole) }}</span>
                        </div>
                        @if($currentRole === 'admin')
                        <div class="section-subtitle text-success mt-2">
                            <i class="bi bi-check-circle me-1"></i>
                            <strong>Admin Role:</strong> Full access to all menus by default. Permissions cannot be modified.
                        </div>
                        @else
                        <div class="section-subtitle mt-2">
                            <i class="bi bi-sliders me-1"></i>
                            Configure which menus <strong>{{ ucfirst($currentRole) }}</strong> role can access and what actions it can perform.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="erp-card-body">
                <form action="{{ route('menu-settings.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $currentRole }}">

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:300px;">
                                        <strong><i class="bi bi-list me-2"></i>Menu Name</strong>
                                    </th>
                                    <th style="width:100px;" class="text-center">
                                        <strong>
                                            <span class="badge bg-light text-dark">View</span>
                                        </strong>
                                    </th>
                                    <th style="width:100px;" class="text-center">
                                        <strong>
                                            <span class="badge bg-light text-dark">Create</span>
                                        </strong>
                                    </th>
                                    <th style="width:100px;" class="text-center">
                                        <strong>
                                            <span class="badge bg-light text-dark">Edit</span>
                                        </strong>
                                    </th>
                                    <th style="width:100px;" class="text-center">
                                        <strong>
                                            <span class="badge bg-light text-dark">Delete</span>
                                        </strong>
                                    </th>
                                    <th style="width:100px;" class="text-center">
                                        <strong>
                                            <span class="badge bg-light text-dark">Approve</span>
                                        </strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menuKeys as $key)
                                @php $perm = $permissions[$key] ?? null; @endphp
                                <tr style="vertical-align:middle;">
                                    <td>
                                        <div>
                                            <strong style="color:#2c3e50;font-size:0.95rem;">
                                                {{ $menuLabels[$key] ?? ucwords(str_replace('-', ' ', $key)) }}
                                            </strong>
                                            <br>
                                            <small class="text-muted">{{ $key }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input cursor-pointer"
                                                   name="permissions[{{ $key }}][can_view]" value="1"
                                                   {{ ($perm && $perm->can_view) || $currentRole === 'admin' ? 'checked' : '' }}
                                                   {{ $currentRole === 'admin' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input cursor-pointer"
                                                   name="permissions[{{ $key }}][can_create]" value="1"
                                                   {{ ($perm && $perm->can_create) || $currentRole === 'admin' ? 'checked' : '' }}
                                                   {{ $currentRole === 'admin' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input cursor-pointer"
                                                   name="permissions[{{ $key }}][can_edit]" value="1"
                                                   {{ ($perm && $perm->can_edit) || $currentRole === 'admin' ? 'checked' : '' }}
                                                   {{ $currentRole === 'admin' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input cursor-pointer"
                                                   name="permissions[{{ $key }}][can_delete]" value="1"
                                                   {{ ($perm && $perm->can_delete) || $currentRole === 'admin' ? 'checked' : '' }}
                                                   {{ $currentRole === 'admin' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input cursor-pointer"
                                                   name="permissions[{{ $key }}][can_approve]" value="1"
                                                   {{ ($perm && $perm->can_approve) || $currentRole === 'admin' ? 'checked' : '' }}
                                                   {{ $currentRole === 'admin' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($currentRole !== 'admin')
                    <div class="mt-4 pt-3 border-top d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-danger" style="border-radius:8px;">
                            <i class="bi bi-check-circle me-1"></i>Save Permissions
                        </button>
                        <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;"
                                onclick="document.querySelectorAll('input[type=checkbox]:not(:disabled)').forEach(c=>c.checked=true)">
                            <i class="bi bi-check2-all me-1"></i>Select All
                        </button>
                        <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;"
                                onclick="document.querySelectorAll('input[type=checkbox]:not(:disabled)').forEach(c=>c.checked=false)">
                            <i class="bi bi-x-circle me-1"></i>Clear All
                        </button>
                    </div>
                    @else
                    <div class="mt-4 p-3 bg-light border border-warning rounded" style="border-radius:8px;">
                        <i class="bi bi-shield-check text-warning me-2"></i>
                        <strong>Admin Role Protection:</strong> The admin role has full permissions for all menus and cannot be modified.
                        Only edit permissions for other roles.
                    </div>
                    @endif
                </form>
            </div>
=======
{{-- Role Selector --}}
<div class="erp-card mb-3">
    <div class="erp-card-body py-2">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <strong style="font-size:.9rem;">Select Role:</strong>
            @foreach($roles as $r)
            <a href="{{ route('menu-settings.index', ['role' => $r]) }}" class="btn btn-sm {{ $currentRole === $r ? 'btn-danger' : 'btn-outline-secondary' }}" style="border-radius:10px;">{{ ucfirst($r) }}</a>
            @endforeach
            <form action="{{ route('menu-settings.add-role') }}" method="POST" class="d-flex gap-1">
                @csrf
                <input type="text" name="new_role" class="form-control form-control-sm" placeholder="New role..." style="border-radius:10px;width:120px;" required>
                <button class="btn btn-sm btn-outline-danger" style="border-radius:10px;">+ Add</button>
            </form>
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
        </div>
    </div>
</div>

<<<<<<< HEAD
<style>
    .cursor-pointer { cursor: pointer; }
</style>
@endsection

=======
{{-- Permission Matrix --}}
<div class="erp-card">
    <div class="erp-card-header">
        <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Permissions for: <span class="text-danger">{{ ucfirst($currentRole) }}</span></div>
        @if($currentRole === 'admin')
        <div class="section-subtitle text-success"><i class="bi bi-check-circle me-1"></i>Admin has full access to all menus by default.</div>
        @endif
    </div>
    <div class="erp-card-body">
        <form action="{{ route('menu-settings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="{{ $currentRole }}">

            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width:250px;">Menu</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Create</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center">Approve</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menuKeys as $key)
                        @php $perm = $permissions[$key] ?? null; @endphp
                        <tr>
                            <td><strong>{{ $menuLabels[$key] ?? ucwords(str_replace('-', ' ', $key)) }}</strong></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="permissions[{{ $key }}][can_view]" value="1" {{ ($perm && $perm->can_view) || $currentRole === 'admin' ? 'checked' : '' }}></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="permissions[{{ $key }}][can_create]" value="1" {{ ($perm && $perm->can_create) || $currentRole === 'admin' ? 'checked' : '' }}></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="permissions[{{ $key }}][can_edit]" value="1" {{ ($perm && $perm->can_edit) || $currentRole === 'admin' ? 'checked' : '' }}></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="permissions[{{ $key }}][can_delete]" value="1" {{ ($perm && $perm->can_delete) || $currentRole === 'admin' ? 'checked' : '' }}></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="permissions[{{ $key }}][can_approve]" value="1" {{ ($perm && $perm->can_approve) || $currentRole === 'admin' ? 'checked' : '' }}></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-danger" style="border-radius:12px;"><i class="bi bi-check-lg me-1"></i>Save Permissions</button>
                <button type="button" class="btn btn-outline-secondary" style="border-radius:12px;" onclick="document.querySelectorAll('input[type=checkbox]').forEach(c=>c.checked=true)">Select All</button>
                <button type="button" class="btn btn-outline-secondary" style="border-radius:12px;" onclick="document.querySelectorAll('input[type=checkbox]').forEach(c=>c.checked=false)">Deselect All</button>
            </div>
        </form>
    </div>
</div>
@endsection
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
