@extends('layouts.app')
@section('page-title', 'Opname Stok')
@section('breadcrumb')<li class="breadcrumb-item active">Opname Stok</li>@endsection

@section('content')
<x-card>
    <x-slot:header>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="section-title"><i class="bi bi-clipboard2-data me-2"></i>Daftar Opname Stok</div>
            <a href="{{ route('stock-opname.create') }}" class="btn btn-danger btn-sm"><i class="bi bi-plus-lg me-1"></i>Opname Baru</a>
        </div>
    </x-slot:header>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nomor opname..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                @foreach(['in_progress'=>'In Progress','pending_approval'=>'Menunggu Approval','completed'=>'Selesai'] as $key=>$label)
                    <option value="{{ $key }}" {{ request('status')==$key?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary btn-sm">Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-modern mb-0">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Progress</th>
                    <th>Dilakukan Oleh</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($opnames as $o)
                <tr>
                    <td><a href="{{ route('stock-opname.show', $o) }}" style="font-weight:700;">{{ $o->opname_number }}</a></td>
                    <td>{{ $o->opname_date->format('d M Y') }}</td>
                    <td>{{ $o->items_count }}</td>
                    <td>
                        @if($o->status === 'in_progress')
                            <span class="text-muted" style="font-size:.8rem;">
                                <i class="bi bi-clock me-1"></i>Sedang berjalan
                            </span>
                        @elseif(in_array($o->status, ['pending_approval','completed']))
                            <span class="text-success" style="font-size:.8rem;">
                                <i class="bi bi-check-circle me-1"></i>Selesai dihitung
                            </span>
                        @endif
                    </td>
                    <td>{{ $o->conductor->name ?? '-' }}</td>
                    <td>
                        @php $bm=['in_progress'=>'warning','pending_approval'=>'info','completed'=>'success']; $lm=['in_progress'=>'In Progress','pending_approval'=>'Menunggu Approval','completed'=>'Selesai']; @endphp
                        <span class="badge bg-{{ $bm[$o->status]??'secondary' }}">{{ $lm[$o->status]??$o->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('stock-opname.show', $o) }}" class="btn btn-sm btn-light" title="Lihat/Hitung"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada catatan opname stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $opnames->links() }}</div>
</x-card>
@endsection
