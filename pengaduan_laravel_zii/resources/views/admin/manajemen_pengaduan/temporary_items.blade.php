@extends('layouts.app')

@section('title', 'Manajemen Request Item Baru')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">📦 Request Item Baru</h3>
                <p class="text-muted mb-0">Kelola request item baru dari pengguna</p>
            </div>
            
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FILTER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.temp.items') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control"
                                       placeholder="Cari nama item atau lokasi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL REQUEST ITEM --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Daftar Request Item Baru</h5>
                <span class="badge bg-light text-dark">
                    Total: {{ $temporaryItems->count() }} request
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Nama Item Baru</th>
                                <th>Lokasi</th>
                                <th>Pengguna</th>
                                <th>Pengaduan Terkait</th>
                                <th>Status</th>
                                <th width="250" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($temporaryItems as $key => $item)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $item->nama_barang_baru }}</strong>
                                </td>
                                <td>{{ $item->lokasi_barang_baru }}</td>
                                <td>{{ $item->user->nama_pengguna ?? '-' }}</td>
                                <td>
                                    @if($item->pengaduan)
                                        <a href="{{ route('admin.pengaduan.show', $item->id_pengaduan) }}"
                                           class="text-primary text-decoration-none" title="Lihat detail pengaduan">
                                            {{ Str::limit($item->pengaduan->nama_pengaduan, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($item->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                        @if($item->alasan_penolakan)
                                            <i class="fas fa-info-circle text-danger ms-1"
                                               data-bs-toggle="tooltip" data-bs-placement="top"
                                               title="Alasan: {{ $item->alasan_penolakan }}"></i>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'pending')
                                        <div class="d-flex gap-2 justify-content-center">
                                            <form action="{{ route('admin.temp.items.approve', $item->id_temporary) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Setujui item **{{ $item->nama_barang_baru }}**? Ini akan menambahkannya ke inventaris.')">
                                                    <i class="fas fa-check me-1"></i>Setujui
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $item->id_temporary }}">
                                                <i class="fas fa-times me-1"></i>Tolak
                                            </button>

                                            {{-- Tombol Hapus untuk Pending --}}
                                            <form action="{{ route('admin.temp.items.destroy', $item->id_temporary) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Yakin ingin menghapus item **{{ $item->nama_barang_baru }}**? Aksi ini tidak bisa dibatalkan.')"
                                                        title="Hapus permanen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>

                                        <div class="modal fade" id="rejectModal{{ $item->id_temporary }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Request Item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.temp.items.reject', $item->id_temporary) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Anda akan menolak request item: <strong>{{ $item->nama_barang_baru }}</strong></p>
                                                            <div class="mb-3">
                                                                <label for="alasan_penolakan" class="form-label">Alasan Penolakan</label>
                                                                <textarea name="alasan_penolakan" class="form-control" rows="3"
                                                                            placeholder="Berikan alasan penolakan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($item->status == 'rejected')
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <span class="text-muted fst-italic me-2">Ditolak</span>

                                            {{-- Tombol Hapus untuk Rejected --}}
                                            <form action="{{ route('admin.temp.items.destroy', $item->id_temporary) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Yakin ingin menghapus item **{{ $item->nama_barang_baru }}**? Aksi ini tidak bisa dibatalkan.')"
                                                        title="Hapus permanen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        {{-- Disetujui --}}
                                        <span class="text-success fst-italic">Disetujui</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">Tidak ada request item baru</p>
                                    </div>
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

<script>
    // Inisialisasi Tooltip Bootstrap (Jika Anda menggunakan Bootstrap 5)
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
