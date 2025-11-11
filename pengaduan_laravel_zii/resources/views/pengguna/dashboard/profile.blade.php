@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
<div class="content-container">
    {{-- Notifikasi (Jika ada: sukses, error, dll.) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-card mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom animate-card mb-4">
        <div class="card-body text-center p-5">
            <div class="user-avatar mx-auto mb-4">
                <div class="avatar-container">
                    {{-- Ganti URL ikon default dengan path gambar default Anda --}}
                    <img src="https://img.icons8.com/fluency/96/000000/user-male-circle.png"
                         alt="Foto Profil"
                         class="avatar-image">
                    {{-- Overlay untuk upload foto (diaktifkan di CSS) --}}
                    <div class="avatar-overlay" title="Ubah Foto">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
            </div>
            <h3 class="text-primary mb-2">{{ $user->nama_pengguna }}</h3>
            <span class="badge bg-primary fs-6 text-white">
                <i class="fas fa-user-shield me-1"></i>{{ ucfirst($user->role) }}
            </span>
        </div>
    </div>

    <div class="card card-custom animate-card mb-4" style="animation-delay: 0.1s">
        <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-3 text-primary">
                <i class="fas fa-user-circle me-2"></i>Informasi Akun
            </h5>
            <a href="{{ route('user.profile.edit') }}" class="btn btn-primary btn-custom btn-sm">
                <i class="fas fa-edit me-2"></i>Edit Profil
            </a>
        </div>

        <div class="card-body pt-3">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon bg-primary-50">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div class="info-content">
                        <label class="info-label text-muted">Nama Pengguna</label>
                        <div class="info-value text-dark">{{ $user->nama_pengguna }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon bg-warning-50">
                        <i class="fas fa-at text-warning"></i>
                    </div>
                    <div class="info-content">
                        <label class="info-label text-muted">Username</label>
                        <div class="info-value text-dark">{{ $user->username }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon bg-success-50">
                        <i class="fas fa-envelope text-success"></i>
                    </div>
                    <div class="info-content">
                        <label class="info-label text-muted">Email</label>
                        <div class="info-value text-dark">{{ $user->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon bg-info-50">
                        <i class="fas fa-user-tag text-info"></i>
                    </div>
                    <div class="info-content">
                        <label class="info-label text-muted">Role</label>
                        <div class="info-value">
                            <span class="badge bg-primary text-white">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* --- STYLES --- */

/* Avatar Styles */
.avatar-container {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.avatar-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 4px solid var(--primary);
    object-fit: cover;
    background: white;
    box-shadow: var(--shadow);
}

.avatar-overlay {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 32px;
    height: 32px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    border: 2px solid white;
    opacity: 0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.avatar-container:hover .avatar-overlay {
    opacity: 1;
    transform: scale(1.1);
}

/* Info Grid Styles */
.info-grid {
    /* MENGATUR LAYOUT MENJADI 2 KOLOM UNTUK 4 KOTAK */
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem; /* Jarak antar kotak */
}

.info-item {
    display: flex;
    align-items: center;
    padding: 1rem; /* Mengurangi padding untuk membuat kotak lebih ringkas */
    background: var(--light-bg);
    border-radius: 12px;
    border: 1px solid var(--border);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.info-icon {
    width: 45px; /* Sedikit dikecilkan */
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* Warna Latar Belakang Ikon */
.bg-primary-50 { background: #e0f7fa; }
.bg-warning-50 { background: #fff3e0; }
.bg-success-50 { background: #e8f5e9; }
.bg-info-50 { background: #e3f2fd; }

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.7rem; /* Dikecilkan */
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}

.info-value {
    font-size: 0.95rem; /* Dikecilkan */
    font-weight: 600;
    color: var(--text-primary);
}

/* Buttons */
.btn-custom {
    border-radius: 10px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}
.btn-outline-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    border: 2px solid var(--border);
    color: var(--text-secondary);
    background: transparent;
}

/* Animation */
.animate-card {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 992px) { /* Medium device atau lebih kecil */
    .info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .avatar-container {
        width: 80px;
        height: 80px;
    }
}
</style>

<script>
// --- SCRIPTS ---
document.addEventListener('DOMContentLoaded', function() {
    // 1. Add animation delays dynamically
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${(index * 0.1) + 0.1}s`;
    });

    // 2. Avatar click handler (for future photo upload feature)
    const avatarOverlay = document.querySelector('.avatar-overlay');
    if (avatarOverlay) {
        avatarOverlay.addEventListener('click', function() {
            // Implement photo upload functionality here
            console.log('Avatar upload clicked');
        });
    }
});
</script>
@endsection
