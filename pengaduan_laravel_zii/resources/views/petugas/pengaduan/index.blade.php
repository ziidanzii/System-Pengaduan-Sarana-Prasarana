@extends('layouts.petugas')

@section('content')
<div class="content-container">
    <!-- Header -->
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2 text-primary fw-bold">Daftar Pengaduan Disetujui</h2>
                    <p class="text-muted mb-0">Berikut daftar pengaduan yang sudah disetujui admin dan siap diproses</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-inbox me-2"></i>Total: {{ $pengaduan->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card card-custom animate-card" style="animation-delay: 0.2s">
        <div class="card-header bg-transparent border-bottom pb-3">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-list me-2"></i>Daftar Semua Pengaduan
            </h5>
        </div>
        <div class="card-body p-0">
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
                                <th>Status</th>
                                <th>Saran Petugas</th>
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
                                        <small class="text-muted">{{ Str::limit($p->deskripsi, 60) }}</small>
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
                                    <span class="complaint-status status-{{ strtolower($p->status) }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($p->saran_petugas)
                                        <span class="text-muted small">{{ Str::limit($p->saran_petugas, 50) }}</span>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
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
</div>

<style>
/* Table Styling */
.table {
    margin-bottom: 0;
    border-radius: 12px;
    overflow: hidden;
}

.table thead th {
    border-bottom: 2px solid var(--border);
    font-weight: 600;
    color: var(--text-secondary);
    padding: 1rem 0.75rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--light-bg);
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
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
    display: inline-block;
    min-width: 100px;
    text-align: center;
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
    border: none;
    box-shadow: var(--shadow);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Empty State */
.empty-state {
    padding: 3rem 2rem;
}

.empty-state i {
    opacity: 0.5;
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

/* Alert Styling */
.alert {
    border: none;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border-left: 4px solid #10b981;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }

    .complaint-status {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
        min-width: 80px;
    }

    .btn-action {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
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
        background: white;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
        border: none;
        padding: 0.5rem 0;
    }

    .table tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        min-width: 100px;
    }

    .table tbody td:last-child {
        justify-content: center;
        border-top: 1px solid var(--border-light);
        padding-top: 1rem;
        margin-top: 0.5rem;
    }

    /* Add data labels for mobile */
    .table tbody td:nth-child(1):before { content: "No"; }
    .table tbody td:nth-child(2):before { content: "Pengaduan"; }
    .table tbody td:nth-child(3):before { content: "Lokasi"; }
    .table tbody td:nth-child(4):before { content: "Pengaju"; }
    .table tbody td:nth-child(5):before { content: "Status"; }
    .table tbody td:nth-child(6):before { content: "Saran"; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation delays
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Make table rows clickable on desktop
    const tableRows = document.querySelectorAll('.complaint-row');
    tableRows.forEach(row => {
        const link = row.querySelector('a[href]');
        if (link && window.innerWidth > 576) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                if (!e.target.closest('a, button')) {
                    link.click();
                }
            });
        }
    });

    // Add data labels for mobile responsive
    if (window.innerWidth <= 576) {
        const headers = document.querySelectorAll('thead th');
        const headerTexts = Array.from(headers).map(th => th.textContent.trim());

        document.querySelectorAll('tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (headerTexts[index]) {
                    cell.setAttribute('data-label', headerTexts[index]);
                }
            });
        });
    }
});
</script>
@endsection
