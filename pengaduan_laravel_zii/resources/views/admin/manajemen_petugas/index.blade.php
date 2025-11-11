@extends('layouts.app')

@section('title', 'Manajemen Petugas')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">🛠️ Manajemen Petugas</h3>
                <p class="text-muted mb-0">Kelola data petugas sistem</p>
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



        {{-- TOMBOL TAMBAH & PENCARIAN --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.petugas.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Petugas
                </a>
            </div>

            {{-- PENCARIAN --}}
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama petugas...">
            </div>
        </div>

        {{-- TABEL PETUGAS --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Daftar Petugas</h5>
                <span class="badge bg-light text-dark">
                    Total: {{ $petugas->count() }} petugas
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="petugasTable">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Petugas</th>
                                <th>Kontak & Gender</th>
                                <th>Username</th>
                                <th>Tanggal Bergabung</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($petugas as $key => $p)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <span class="text-white fw-bold small">
                                                {{ substr($p->nama, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $p->nama }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark mb-1">
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            {{ $p->telp }}
                                        </span>
                                        <span class="text-muted small">
                                            @if($p->gender == 'L')
                                                <i class="fas fa-mars text-info me-1"></i>Laki-laki
                                            @else
                                                <i class="fas fa-venus text-warning me-1"></i>Perempuan
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $p->user->username ?? '-' }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.petugas.edit', $p->id_petugas) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit Petugas">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.petugas.destroy', $p->id_petugas) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin hapus petugas {{ $p->nama }}?')">
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
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="py-4">
                                        <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">Belum ada petugas</p>
                                        <p class="small">Klik "Tambah Petugas" untuk menambahkan petugas pertama</p>
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

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.7rem 1.5rem rgba(0,0,0,0.15);
    }
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
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@push('scripts')
<script>
    // Pencarian real-time
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#petugasTable tbody tr');

        rows.forEach(row => {
            const namaPetugas = row.cells[1].textContent.toLowerCase();
            if (namaPetugas.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

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
