@extends('layouts.app')
@section('page-title', 'Menu & Role Settings')
@section('breadcrumb')<li class="breadcrumb-item active">Menu & Roles</li>@endsection

@section('content')
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
        </div>
    </div>
</div>

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
