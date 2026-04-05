@php
    $user = $user ?? (object)['name' => '', 'email' => '', 'role' => '', 'department' => '', 'is_active' => true];
@endphp

<div class="form-section mb-4">
    <h6 class="text-muted mb-3"><i class="bi bi-person-circle me-2"></i>Account Information</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Full Name</label>
            <input type="text" name="name" class="form-control" style="border-radius:8px;"
                   value="{{ old('name', $user->name ?? '') }}" placeholder="e.g., John Doe" required>
            @error('name')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" style="border-radius:8px;"
                   value="{{ old('email', $user->email ?? '') }}" placeholder="user@example.com" required>
            @error('email')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Department</label>
            <input type="text" name="department" class="form-control" style="border-radius:8px;"
                   value="{{ old('department', $user->department ?? '') }}" placeholder="e.g., Logistic, Plant, Warehouse">
            @error('department')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
    </div>
</div>

<div class="form-section mb-4">
    <h6 class="text-muted mb-3"><i class="bi bi-shield-lock me-2"></i>Role & Security</h6>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-bold">
                <i class="bi bi-person-badge me-2"></i>Select User Role
            </label>
            <div class="row g-2">
                <div class="col-md-6">
                    <select name="role" class="form-select" style="border-radius:8px;padding:10px 12px;" required>
                        <option value="">-- Choose a role --</option>
                        @foreach($roles as $roleKey => $roleLabel)
                            <option value="{{ $roleKey }}" {{ old('role', $user->role ?? '') === $roleKey ? 'selected' : '' }}>
                                {{ $roleLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block" style="padding-top:10px;">
                        <i class="bi bi-info-circle me-1"></i>Role determines menu access and permissions.
                        <br><strong>Configure roles in Settings → Menu & Roles</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-section mb-4">
    <h6 class="text-muted mb-3"><i class="bi bi-lock me-2"></i>Password</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">
                Password {{ isset($user->id) ? '<span class="text-muted">(optional)</span>' : '' }}
            </label>
            <input type="password" name="password" class="form-control" style="border-radius:8px;"
                   {{ !isset($user->id) ? 'required' : '' }}
                   placeholder="{{ isset($user->id) ? 'Leave empty to keep current password' : 'Minimum 8 characters' }}">
            @error('password')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" style="border-radius:8px;"
                   {{ !isset($user->id) ? 'required' : '' }}
                   placeholder="Retype password">
            @error('password_confirmation')<small class="text-danger d-block mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h6 class="text-muted mb-3"><i class="bi bi-toggles me-2"></i>Account Status</h6>
    <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
               {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
               style="cursor:pointer;width:3em;height:1.5em;">
        <label class="form-check-label" for="is_active" style="cursor:pointer;">
            <strong>Active Account</strong>
            <span class="text-muted d-block" style="font-size:0.85rem; margin-top:0.25rem;">
                <i class="bi bi-info-circle me-1"></i>User can login only when active
            </span>
        </label>
    </div>
</div>
