@extends('layouts.app')

@section('title', 'Manajemen Items')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">📦 Manajemen Items</h3>
                <p class="text-muted mb-0">Kelola data barang dan sarana prasarana</p>
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

        {{-- FILTER DAN PENCARIAN --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.items.index') }}" method="GET" id="filter-form">
                    <div class="row g-3 align-items-end">
                        {{-- Pencarian --}}
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari Items</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Cari nama item atau deskripsi..." value="{{ request('search') }}">
                        </div>
                        {{-- Tombol Aksi --}}
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('admin.items.create') }}" class="btn btn-success flex-fill">
                                <i class="fas fa-plus me-2"></i>Tambah Item
                            </a>
                            @if(request('search'))
                                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-2"></i>Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL ITEMS --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Daftar Items</h5>
                <span class="badge bg-light text-dark">
                    Total: {{ $items->total() }} items
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Nama Item</th>
                                <th>Deskripsi</th>
                                <th width="120" class="text-center">Foto</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $key => $item)
                            <tr>
                                <td class="text-center">{{ ($items->currentPage() - 1) * $items->perPage() + $key + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $item->nama_item }}</strong>
                                </td>
                                <td>
                                    @if($item->deskripsi)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                              title="{{ $item->deskripsi }}">
                                            {{ Str::limit($item->deskripsi, 60) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->foto)
                                        <img src="{{ asset('storage/'.$item->foto) }}"
                                             class="rounded shadow-sm"
                                             width="60"
                                             height="60"
                                             style="object-fit: cover;"
                                             alt="{{ $item->nama_item }}"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal"
                                             onclick="showImage('{{ asset('storage/'.$item->foto) }}', '{{ $item->nama_item }}')">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.items.edit', $item->id_item) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit Item">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.items.destroy', $item->id_item) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin hapus item {{ $item->nama_item }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <div class="py-4">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">Belum ada items</p>
                                        @if(request('search'))
                                            <p class="small">Tidak ditemukan items dengan kata kunci "{{ request('search') }}"</p>
                                            <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-primary mt-2">
                                                Tampilkan Semua Items
                                            </a>
                                        @else
                                            <p class="small">Klik "Tambah Item" untuk menambahkan item pertama</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}

            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK FOTO --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid rounded">
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
    .table img {
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .table img:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
    function showImage(imageSrc, title) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalLabel').textContent = title;
    }

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
