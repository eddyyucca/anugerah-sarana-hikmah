@extends('layouts.app')
@section('page-title', 'Fit to Work')
@section('breadcrumb')
<li class="breadcrumb-item active">Fit to Work</li>
@endsection

@section('content')
<x-alerts />

{{-- Filter --}}
<x-card class="mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nomor / operator..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <select name="shift" class="form-select form-select-sm">
                <option value="">Semua Shift</option>
                <option value="day" {{ request('shift') === 'day' ? 'selected' : '' }}>Pagi</option>
                <option value="night" {{ request('shift') === 'night' ? 'selected' : '' }}>Malam</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="fit" {{ request('status') === 'fit' ? 'selected' : '' }}>Fit</option>
                <option value="unfit" {{ request('status') === 'unfit' ? 'selected' : '' }}>Unfit</option>
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-sm btn-danger"><i class="bi bi-search"></i></button>
            <a href="{{ route('fit-to-work.index') }}" class="btn btn-sm btn-light"><i class="bi bi-x"></i></a>
        </div>
    </form>
</x-card>

{{-- Table --}}
<x-card>
    <x-slot:header>
        <div class="d-flex justify-content-between align-items-center">
            <div class="section-title"><i class="bi bi-heart-pulse me-2"></i>Daftar Fit to Work</div>
            <a href="{{ route('fit-to-work.create') }}" class="btn btn-sm btn-danger">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
        </div>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.87rem;">
            <thead class="table-light">
                <tr>
                    <th>No. FTW</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Operator</th>
                    <th>Tekanan Darah</th>
                    <th>Alkohol</th>
                    <th>Status</th>
                    <th>Pemeriksa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($checks as $ftw)
                <tr>
                    <td><span class="fw-semibold text-danger">{{ $ftw->ftw_number }}</span></td>
                    <td>{{ $ftw->check_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $ftw->shift === 'day' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                            {{ $ftw->shift === 'day' ? 'Pagi' : 'Malam' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $ftw->operator->operator_name }}</div>
                        <small class="text-muted">{{ $ftw->operator->operator_code }}</small>
                    </td>
                    <td>{{ $ftw->blood_pressure ?: '-' }}</td>
                    <td>
                        @if($ftw->alcohol_test)
                            <span class="badge bg-danger">Terdeteksi</span>
                        @else
                            <span class="badge bg-success">Negatif</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $ftw->status_color }} fs-6 px-3">{{ $ftw->status_label }}</span>
                    </td>
                    <td><small>{{ $ftw->checker?->name ?? '-' }}</small></td>
                    <td>
                        <a href="{{ route('fit-to-work.show', $ftw) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data Fit to Work.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($checks->hasPages())
    <div class="mt-3">{{ $checks->links() }}</div>
    @endif
</x-card>
@endsection
