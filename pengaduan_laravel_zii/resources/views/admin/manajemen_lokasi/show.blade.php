@extends('layouts.app')

@section('title', 'Detail Lokasi - ' . $lokasi->nama_lokasi)

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">📍 {{ $lokasi->nama_lokasi }}</h3>
                <p class="text-muted mb-0">Kelola item-item di lokasi ini</p>
            </div>
            
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM EDIT LOKASI --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">✏️ Edit Nama Lokasi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lokasi.update', $lokasi->id_lokasi) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" class="form-control"
                                   value="{{ $lokasi->nama_lokasi }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-save me-2"></i>Update Lokasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            {{-- FORM TAMBAH ITEM --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">➕ Tambah Item ke Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.lokasi.add-item', $lokasi->id_lokasi) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="id_item" class="form-label">Pilih Item</label>
                                <select name="id_item" id="id_item" class="form-select" required>
                                    <option value="">-- Pilih Item --</option>
                                    @foreach($items as $item)
                                        @if(!in_array($item->id_item, $itemsInLocation))
                                            <option value="{{ $item->id_item }}">{{ $item->nama_item }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>Tambah ke Lokasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- DAFTAR ITEM DI LOKASI --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📦 Item di Lokasi Ini</h5>
                        <span class="badge bg-light text-dark">
                            {{ $lokasi->items_count }} items
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Nama Item</th>
                                        <th>Deskripsi</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lokasi->items as $key => $item)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $item->nama_item }}</strong>
                                        </td>
                                        <td>
                                            @if($item->deskripsi)
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ Str::limit($item->deskripsi, 50) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.lokasi.remove-item', [$lokasi->id_lokasi, $item->id_item]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin hapus item dari lokasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari Lokasi">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Belum ada item di lokasi ini</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td {
        vertical-align: middle;
    }
    .card {
        border-radius: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto close alerts setelah 5 detik
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush
@endsection
