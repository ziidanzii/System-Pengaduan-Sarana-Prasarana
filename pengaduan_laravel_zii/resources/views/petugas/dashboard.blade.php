@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="content-container">
    <!-- Welcome Card -->
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2 text-primary fw-bold">Selamat Datang, {{ Auth::user()->nama_pengguna ?? 'Petugas' }}! 👋</h3>
                    <p class="text-muted mb-0">Kelola pengaduan sarana dan prasarana dengan efisien</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-user-cog me-2"></i>Petugas
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Pengaduan Masuk -->
    <div class="card card-custom animate-card" style="animation-delay: 0.4s">
        <div class="card-header bg-transparent border-bottom pb-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-inbox me-2"></i>Pengaduan Masuk Terbaru
                </h5>
                <p class="text-muted mb-0 mt-1">Pengaduan yang baru diajukan oleh pengguna</p>
            </div>
            <a href="{{ route('petugas.pengaduan.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-list me-1"></i>Lihat Semua
            </a>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif

            @if($pengaduan->isEmpty())
                <div class="text-center py-5 empty-state">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-dark mb-2">Belum Ada Pengaduan</h5>
                    <p class="text-muted">Tidak ada pengaduan yang perlu ditangani saat ini</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Pengaduan</th>
                                <th>Lokasi</th>
                                <th>Pengaju</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengaduan as $p)
                            <tr class="complaint-row">
                                <td class="ps-4 fw-medium">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $p->nama_pengaduan }}</span>
                                        <small class="text-muted">{{ Str::limit($p->deskripsi, 50) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        {{ $p->lokasi }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $p->user->nama_pengguna ?? '-' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="complaint-status status-{{ strtolower($p->status) }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('petugas.pengaduan.show', $p->id_pengaduan) }}"
                                       class="btn btn-primary btn-sm btn-action">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats & Info -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card card-custom animate-card h-100" style="animation-delay: 0.5s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-warning fw-bold">
                        <i class="fas fa-chart-bar me-2"></i>Ringkasan Bulan Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-number-sm text-primary">{{ $jumlahDiajukan }}</div>
                            <div class="stat-label-sm">Diajukan</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-number-sm text-warning">{{ $jumlahDiproses }}</div>
                            <div class="stat-label-sm">Diproses</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-number-sm text-success">{{ $jumlahSelesai }}</div>
                            <div class="stat-label-sm">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom animate-card h-100" style="animation-delay: 0.6s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <div>
                            <small class="fw-semibold d-block">Respon Cepat</small>
                            <small class="text-muted">Tanggapi pengaduan dalam 24 jam</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock text-warning me-3"></i>
                        <div>
                            <small class="fw-semibold d-block">Update Status</small>
                            <small class="text-muted">Perbarui status secara berkala</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Statistics */
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 1.5rem;
    box-shadow: var(--shadow);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
    background: linear-gradient(135deg, currentColor, currentColor);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-number-sm {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-label-sm {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-muted);
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    border-bottom: 2px solid var(--border);
    font-weight: 600;
    color: var(--text-secondary);
    padding: 1rem 0.75rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-light);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.complaint-row {
    transition: all 0.3s ease;
}

.complaint-row:hover {
    background: var(--light-bg);
}

/* Complaint Status */
.complaint-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    box-shadow: var(--shadow);
}

.status-diajukan {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}
.status-disetujui {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
}
.status-diproses {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.status-selesai {
    background: linear-gradient(135deg, #10b981, #059669);
}
.status-ditolak {
    background: linear-gradient(135deg, #ef4444, #b91c1c);
}

/* Buttons */
.btn-action {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    box-shadow: var(--shadow);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Empty State */
.empty-state {
    padding: 3rem 2rem;
}

/* Animation */
.animate-card {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.5s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .table-responsive {
        font-size: 0.9rem;
    }

    .complaint-status {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
    }
}

@media (max-width: 576px) {
    .stat-number {
        font-size: 1.5rem;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
    }

    .table tbody td {
        display: block;
        text-align: left;
        border: none;
        padding: 0.5rem 0;
    }

    .table tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-muted);
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
        text-transform: uppercase;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation delays
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Make table rows clickable
    const tableRows = document.querySelectorAll('.complaint-row');
    tableRows.forEach(row => {
        const link = row.querySelector('a[href]');
        if (link) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                if (!e.target.closest('a, button')) {
                    link.click();
                }
            });
        }
    });
});
</script>
@endsection
