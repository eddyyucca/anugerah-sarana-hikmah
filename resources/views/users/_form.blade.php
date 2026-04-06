@php
    $user = $user ?? (object)['name' => '', 'email' => '', 'role' => '', 'department' => '', 'is_active' => true];
    $isEdit = isset($user->id);
@endphp

{{-- Avatar Preview --}}
<div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
    <div id="avatarPreview" class="user-form-avatar">
        {{ strtoupper(substr(old('name', $user->name ?? 'U'), 0, 2)) }}
    </div>
    <div>
        <div class="fw-bold" style="font-size:.95rem;" id="avatarName">
            {{ old('name', $user->name ?: 'Nama Pengguna') }}
        </div>
        <div class="text-muted" style="font-size:.82rem;">
            {{ $isEdit ? 'Edit akun pengguna' : 'Akun baru' }}
        </div>
    </div>
</div>

{{-- Info Akun --}}
<div class="mb-4">
    <div class="form-section-label">
        <i class="bi bi-person-circle"></i> Informasi Akun
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="nameInput"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name ?? '') }}"
                   placeholder="cth. Budi Santoso" required autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email ?? '') }}"
                   placeholder="email@perusahaan.com" required autocomplete="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Departemen</label>
            <input type="text" name="department"
                   class="form-control @error('department') is-invalid @enderror"
                   value="{{ old('department', $user->department ?? '') }}"
                   placeholder="cth. Logistik, Workshop, Gudang">
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">-- Pilih Role --</option>
                @foreach($roles as $roleKey => $roleLabel)
                <option value="{{ $roleKey }}" {{ old('role', $user->role ?? '') === $roleKey ? 'selected' : '' }}>
                    {{ $roleLabel }}
                </option>
                @endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Role menentukan akses menu. Atur di <a href="{{ route('menu-settings.index') }}">Menu & Role</a>.</div>
        </div>
    </div>
</div>

{{-- Password --}}
<div class="mb-4">
    <div class="form-section-label">
        <i class="bi bi-lock"></i> Password
        @if($isEdit)
        <span class="badge bg-secondary ms-2" style="font-size:.7rem;font-weight:500;">Opsional</span>
        @endif
    </div>
    @if($isEdit)
    <p class="text-muted mb-3" style="font-size:.83rem;"><i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password.</p>
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Password {!! $isEdit ? '' : '<span class="text-danger">*</span>' !!}
            </label>
            <div class="input-group">
                <input type="password" name="password" id="passwordInput"
                       class="form-control @error('password') is-invalid @enderror"
                       {{ !$isEdit ? 'required' : '' }}
                       placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter' }}"
                       autocomplete="{{ $isEdit ? 'new-password' : 'new-password' }}">
                <button class="btn btn-outline-secondary pw-toggle" type="button" data-target="passwordInput" tabindex="-1">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                Konfirmasi Password {!! $isEdit ? '' : '<span class="text-danger">*</span>' !!}
            </label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="passwordConfirmInput"
                       class="form-control"
                       {{ !$isEdit ? 'required' : '' }}
                       placeholder="Ketik ulang password"
                       autocomplete="new-password">
                <button class="btn btn-outline-secondary pw-toggle" type="button" data-target="passwordConfirmInput" tabindex="-1">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Status Akun --}}
<div>
    <div class="form-section-label">
        <i class="bi bi-toggles"></i> Status Akun
    </div>
    <div class="user-status-card">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <div class="fw-semibold mb-1">Akun Aktif</div>
                <div class="text-muted" style="font-size:.83rem;">Pengguna hanya bisa login jika akun dalam status aktif.</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                       style="width:2.8em;height:1.5em;cursor:pointer;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Avatar preview live update
    const nameInput = document.getElementById('nameInput');
    const avatarEl = document.getElementById('avatarPreview');
    const avatarName = document.getElementById('avatarName');

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            const val = this.value.trim() || 'U';
            avatarEl.textContent = val.substring(0, 2).toUpperCase();
            avatarName.textContent = this.value.trim() || 'Nama Pengguna';
        });
    }

    // Password show/hide toggle
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });
});
</script>
@endpush