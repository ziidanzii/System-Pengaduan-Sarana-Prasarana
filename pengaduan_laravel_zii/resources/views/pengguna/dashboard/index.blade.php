@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="content-container">
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2 text-primary fw-bold">Selamat Datang, {{ $user->nama_pengguna }}! 👋</h3>
                    <p class="text-muted mb-0">Apa yang ingin Anda lakukan hari ini?</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Profil Saya --}}
        <div class="col-md-6 mb-3">
            <div class="card card-custom animate-card h-100" style="animation-delay: 0.3s">
                <div class="card-body text-center p-4">
                    <div class="action-icon mb-3">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h5 class="text-dark fw-semibold mb-2">Profil Saya</h5>
                    <p class="text-muted small mb-3">Kelola informasi akun Anda</p>
                    <a href="{{ route('user.profile') }}" class="btn btn-outline-primary w-100">Kelola</a>
                </div>
            </div>
        </div>

        {{-- Informasi Status --}}
        <div class="col-md-6 mb-3">
            <div class="card card-custom animate-card h-100" style="animation-delay: 0.4s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Informasi Status
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <span class="status-indicator status-new me-3"></span>
                        <small class="text-muted">Diajukan - Menunggu tinjauan</small>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="status-indicator status-process me-3"></span>
                        <small class="text-muted">Diproses - Sedang ditangani</small>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="status-indicator status-done me-3"></span>
                        <small class="text-muted">Selesai - Telah diselesaikan</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-indicator status-rejected me-3"></span>
                        <small class="text-muted">Ditolak - Aduan tidak dapat diproses</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card card-custom animate-card mb-4" style="animation-delay: 0.4s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Aduan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 justify-content-center">
                        <div class="col-6 col-md-2">
                            <div class="stat-number text-primary">{{ $stats['total_aduan'] ?? 0 }}</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-number text-warning">{{ $stats['aduan_diajukan'] ?? 0 }}</div>
                            <div class="stat-label">Diajukan</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-number text-info">{{ $stats['aduan_diproses'] ?? 0 }}</div>
                            <div class="stat-label">Diproses</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-number text-success">{{ $stats['aduan_selesai'] ?? 0 }}</div>
                            <div class="stat-label">Selesai</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-number text-danger">{{ $stats['aduan_ditolak'] ?? 0 }}</div>
                            <div class="stat-label">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-custom animate-card" style="animation-delay: 0.5s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-clock me-2"></i>Aduan Terbaru 
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($aduanList->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada aduan</p>
                            <a href="{{ route('user.aduan.create') }}" class="btn btn-primary">Ajukan Aduan Pertama</a>
                        </div>
                    @else
                        @foreach($aduanList->take(5) as $aduan) {{-- BATAS MAKSIMAL 5 --}}
                        <div class="complaint-item">
                            <div class="complaint-content">
                                <h6 class="complaint-title">{{ $aduan->nama_pengaduan }}</h6>
                                <div class="complaint-date">
                                    {{ \Carbon\Carbon::parse($aduan->created_at)->format('d M Y') }} • {{ $aduan->lokasi }}
                                </div>
                            </div>
                            <span class="complaint-status
                                @if($aduan->status == 'Diajukan' || $aduan->status == 'Disetujui') status-new
                                @elseif($aduan->status == 'Diproses') status-process
                                @elseif($aduan->status == 'Selesai') status-done
                                @elseif($aduan->status == 'Ditolak') status-rejected
                                @else status-new @endif">
                                {{ $aduan->status == 'Disetujui' ? 'Diajukan' : $aduan->status }}
                            </span>
                        </div>
                        @endforeach
                    @endif
                </div>
                @if($aduanList->count() > 5) {{-- Tampilkan footer jika ada lebih dari 5 aduan --}}
                    <div class="card-footer text-center bg-transparent pt-3 pb-2">
                        <a href="{{ route('user.aduan.history') }}" class="small fw-semibold text-primary">Lihat Semua Riwayat Aduan <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.action-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 1.75rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.card-custom:hover .action-icon {
    transform: scale(1.1);
    box-shadow: var(--shadow-lg);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, currentColor, currentColor);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-muted);
}

.complaint-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.complaint-item:hover {
    background: var(--light-bg);
}

.complaint-item:last-child {
    border-bottom: none;
}

.complaint-content {
    flex: 1;
    min-width: 0;
}

.complaint-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
    color: var(--text-primary);
    line-height: 1.4;
}

.complaint-date {
    font-size: 0.8rem;
    color: var(--text-muted);
}

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

.status-new {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}
.status-process {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.status-done {
    background: linear-gradient(135deg, #10b981, #059669);
}
.status-rejected {
    background: linear-gradient(135deg, #ef4444, #c62828);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.quick-links .list-group-item {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    padding: 0.75rem 0;
    transition: all 0.3s ease;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
}

.quick-links .list-group-item:last-child {
    border-bottom: none;
}

.quick-links .list-group-item:hover {
    color: var(--primary);
    background: transparent;
    transform: translateX(5px);
}

.quick-links .list-group-item i {
    font-size: 1.1rem;
    width: 24px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-outline-primary {
    border: 2px solid var(--primary);
    color: var(--primary);
    background: transparent;
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

.card-custom {
    transition: all 0.3s ease;
}

.card-custom:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

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

@media (max-width: 768px) {
    .action-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .complaint-item {
        padding: 0.75rem;
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }

    .complaint-status {
        align-self: center;
    }
}

@media (max-width: 576px) {
    .action-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .complaint-status {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        // Penyesuaian delay untuk menjaga urutan animasi
        card.style.animationDelay = `${(index * 0.1) + 0.3}s`;
    });

    const complaintItems = document.querySelectorAll('.complaint-item');
    complaintItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
             // Logika ini harus disesuaikan jika Anda ingin mengarahkan ke halaman detail aduan
            console.log('Complaint item clicked');
        });
    });
});
</script>
@endsection
