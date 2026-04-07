@extends('layouts.app')
@section('page-title', 'Tambah Fit to Work')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('fit-to-work.index') }}">Fit to Work</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<form action="{{ route('fit-to-work.store') }}" method="POST">
    @csrf
    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-heart-pulse me-2"></i>Pemeriksaan Fit to Work</div>
        </x-slot:header>

        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="No. FTW">
                    <input type="text" class="form-control" value="{{ $ftwNumber }}" readonly style="background:#f8f9fa;">
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Tanggal" required>
                    <input type="date" name="check_date" class="form-control @error('check_date') is-invalid @enderror"
                        value="{{ old('check_date', date('Y-m-d')) }}" required>
                    @error('check_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Shift" required>
                    <select name="shift" class="form-select tom-select @error('shift') is-invalid @enderror" required>
                        <option value="day" {{ old('shift','day') === 'day' ? 'selected' : '' }}>Shift Pagi</option>
                        <option value="night" {{ old('shift') === 'night' ? 'selected' : '' }}>Shift Malam</option>
                    </select>
                    @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Operator" required>
                    <select name="operator_id" class="form-select tom-select @error('operator_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Operator --</option>
                        @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>
                            {{ $op->operator_code }} - {{ $op->operator_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('operator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </x-form-group>
            </div>
        </div>
    </x-card>

    <x-card class="mb-3">
        <x-slot:header>
            <div class="section-title"><i class="bi bi-clipboard2-pulse me-2"></i>Pertanyaan Kesiapan</div>
        </x-slot:header>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Apakah operator siap bekerja?</label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="siap_bekerja" id="siap_ya" value="1" {{ old('siap_bekerja','1') === '1' ? 'checked' : '' }} required>
                        <label class="form-check-label text-success fw-semibold" for="siap_ya">Ya, Siap</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="siap_bekerja" id="siap_tidak" value="0" {{ old('siap_bekerja') === '0' ? 'checked' : '' }}>
                        <label class="form-check-label text-danger fw-semibold" for="siap_tidak">Tidak</label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Apakah kondisi kesehatan operator baik?</label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kondisi_sehat" id="sehat_ya" value="1" {{ old('kondisi_sehat','1') === '1' ? 'checked' : '' }} required>
                        <label class="form-check-label text-success fw-semibold" for="sehat_ya">Ya, Sehat</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kondisi_sehat" id="sehat_tidak" value="0" {{ old('kondisi_sehat') === '0' ? 'checked' : '' }}>
                        <label class="form-check-label text-danger fw-semibold" for="sehat_tidak">Tidak</label>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <x-form-group label="Catatan">
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </x-form-group>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center mt-3 mb-0" style="font-size:.88rem;">
            <i class="bi bi-info-circle me-2"></i>
            Status <strong>Fit</strong> otomatis jika operator <em>siap bekerja</em> <strong>dan</strong> <em>kondisi sehat</em>.
        </div>
    </x-card>

    <div class="d-flex gap-2">
        <x-button type="submit" variant="danger"><i class="bi bi-check-circle me-1"></i>Simpan</x-button>
        <a href="{{ route('fit-to-work.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection
