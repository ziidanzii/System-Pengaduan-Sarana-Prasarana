@extends('layouts.user')

@section('title', 'History Pengaduan')

@section('content')
<div class="content-container">
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-2 text-primary fw-bold">
                        <i class="fas fa-history me-2"></i>History Pengaduan
                    </h3>
                    <p class="text-muted mb-0">Riwayat semua pengaduan yang telah Anda ajukan</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $aduanList->count() }} Pengaduan</span>
                </div>
            </div>
            @php
                $countDiajukan = $aduanList->where('status', 'Diajukan')->count();
                $countDiproses = $aduanList->where('status', 'Diproses')->count();
                $countSelesai  = $aduanList->where('status', 'Selesai')->count();
                $countDitolak  = $aduanList->where('status', 'Ditolak')->count();
            @endphp
            <div class="row mt-3 g-2">
                <div class="col-6 col-md-3">
                    <div class="mini-stat-badge bg-light border">
                        <span class="text-primary fw-bold me-1">{{ $countDiajukan }}</span>
                        <span class="text-muted small">diajukan</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mini-stat-badge bg-light border">
                        <span class="text-warning fw-bold me-1">{{ $countDiproses }}</span>
                        <span class="text-muted small">diproses</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mini-stat-badge bg-light border">
                        <span class="text-success fw-bold me-1">{{ $countSelesai }}</span>
                        <span class="text-muted small">selesai</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mini-stat-badge bg-light border">
                        <span class="text-danger fw-bold me-1">{{ $countDitolak }}</span>
                        <span class="text-muted small">ditolak</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('user.aduan.history') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-dark fw-semibold">Status</label>
                    <select name="status" class="form-control-custom">
                        <option value="">Semua Status</option>
                        <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark fw-semibold">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control-custom" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control-custom" value="{{ request('end_date') }}">
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('user.aduan.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($aduanList->isEmpty())
        <div class="card card-custom animate-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-dark mb-3">Belum Ada History Pengaduan</h4>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['status', 'start_date', 'end_date']))
                        Tidak ada pengaduan yang sesuai dengan filter yang dipilih.
                    @else
                        Anda belum mengajukan pengaduan apapun.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="card card-custom animate-card">
            <div class="card-body p-0">
                @foreach($aduanList as $aduan)
                {{-- Menggunakan route show jika Anda ingin modal mengambil data dari API,
                     tetapi saat ini menggunakan JavaScript lokal --}}
                @php
                    $statusForUser = $aduan->status == 'Disetujui' ? 'Diajukan' : $aduan->status;
                @endphp
                <div class="complaint-item" onclick="showDetailModal({{ $aduan->id_pengaduan }})">
                    <div class="complaint-main">
                        <div class="complaint-icon status-{{ strtolower($statusForUser) }}">
                            <i class="fas
                                @if($statusForUser == 'Diajukan') fa-clock
                                @elseif($aduan->status == 'Ditolak') fa-times-circle
                                @elseif($statusForUser == 'Diproses') fa-cog
                                @elseif($statusForUser == 'Selesai') fa-check
                                @endif
                            "></i>
                        </div>
                        <div class="complaint-content">
                            <h6 class="complaint-title">{{ $aduan->nama_pengaduan }}</h6>
                            <p class="complaint-desc">{{ Str::limit($aduan->deskripsi, 100) }}</p>
                            <div class="complaint-meta">
                                <span><i class="fas fa-map-marker-alt me-1"></i>{{ $aduan->lokasi }}</span>
                                <span><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($aduan->created_at)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="complaint-status status-{{ strtolower($statusForUser) }}">
                        {{ $statusForUser }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between mt-4">

        <a href="{{ route('user.aduan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajukan Pengaduan Baru
        </a>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-3">
                <h5 class="modal-title text-primary fw-bold">Detail Pengaduan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                </div>
            <div class="modal-footer border-0 pt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- --- STYLES --- --}}
<style>
/* Form Controls */
.form-control-custom {
    background: var(--light-card);
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    color: var(--text-primary);
    width: 100%;
}

.form-control-custom:focus {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

/* Complaint Item */
.complaint-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-light);
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
}

.complaint-item:hover {
    background: var(--light-bg);
}

.complaint-item:last-child {
    border-bottom: none;
}

.complaint-main {
    display: flex;
    align-items: center;
    flex: 1;
}

.complaint-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.complaint-content {
    flex: 1;
    min-width: 0;
}

.complaint-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 1rem;
    color: var(--text-primary);
    line-height: 1.4;
}

.complaint-desc {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: var(--text-muted);
    line-height: 1.4;
}

.complaint-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.complaint-meta span {
    display: flex;
    align-items: center;
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
    flex-shrink: 0;
}

/* Status Colors */
.status-diajukan {
    background: linear-gradient(135deg, #3b82f6, #2563eb); /* Biru */
}
.status-diproses {
    background: linear-gradient(135deg, #f59e0b, #d97706); /* Oranye */
}
.status-selesai {
    background: linear-gradient(135deg, #10b981, #059669); /* Hijau Tua */
}
.status-ditolak {
    background: linear-gradient(135deg, #ef4444, #b91c1c); /* Merah */
}

/* Buttons */
.btn {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
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

.btn-outline-secondary {
    border: 2px solid var(--border);
    color: var(--text-secondary);
    background: transparent;
}

.btn-outline-secondary:hover {
    background: var(--light-bg);
    border-color: var(--text-muted);
    color: var(--text-primary);
    transform: translateY(-2px);
}

/* Mini Stat Badges */
.mini-stat-badge {
    display: inline-flex;
    align-items: baseline;
    gap: 0.25rem;
    padding: 0.4rem 0.6rem;
    border-radius: 8px;
    width: 100%;
    justify-content: center;
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
    .complaint-item {
        padding: 1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .complaint-main {
        width: 100%;
    }

    .complaint-status {
        align-self: flex-end;
    }

    .complaint-meta {
        flex-direction: column;
        gap: 0.25rem;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between > * {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .complaint-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .complaint-title {
        font-size: 0.95rem;
    }

    .complaint-desc {
        font-size: 0.85rem;
    }

    .complaint-status {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
    }
}
</style>

{{-- --- SCRIPTS --- --}}
<script>
// Data pengaduan di-render di Blade untuk dipakai JavaScript
const aduanData = {
    @foreach($aduanList as $aduan)
    {{ $aduan->id_pengaduan }}: {
        nama_pengaduan: "{{ $aduan->nama_pengaduan }}",
        deskripsi: `{!! $aduan->deskripsi !!}`,
        lokasi: "{{ $aduan->lokasi }}",
        status: "{{ $aduan->status }}",
        foto: "{{ $aduan->foto }}",
        foto_penyelesaian: "{{ $aduan->foto_penyelesaian }}",
        // PERBAIKAN: Tambahkan saran_petugas dan pastikan aman dari XSS/quote
        saran_petugas: `{!! addslashes($aduan->saran_petugas) !!}`,
        created_at: "{{ \Carbon\Carbon::parse($aduan->created_at)->format('d M Y H:i') }}",
        updated_at: "{{ $aduan->updated_at ? \Carbon\Carbon::parse($aduan->updated_at)->format('d M Y H:i') : '' }}"
    },
    @endforeach
};

// Tampilkan modal detail
function showDetailModal(aduanId) {
    const aduan = aduanData[aduanId];
    if (!aduan) return;

    // Bagian Saran Petugas (hanya ditampilkan jika ada isinya)
    const saranPetugasHTML = aduan.saran_petugas ? `
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="text-primary mb-3 fw-semibold"><i class="fas fa-comment-dots me-2"></i>Saran/Tanggapan Petugas</h6>
                <div class="p-3 bg-light rounded border">
                    <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">${aduan.saran_petugas}</p>
                </div>
            </div>
        </div>
    ` : '';

    // Isi utama modal
    const modalHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3 fw-semibold"><i class="fas fa-info-circle me-2"></i>Informasi Pengaduan</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted fw-semibold" width="40%">Judul</td>
                        <td class="fw-medium">${aduan.nama_pengaduan}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Lokasi</td>
                        <td class="fw-medium">${aduan.lokasi}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Status</td>
                        <td>
                            <span class="complaint-status status-${(aduan.status === 'Disetujui' ? 'Diajukan' : aduan.status).toLowerCase()}">
                                ${(aduan.status === 'Disetujui' ? 'Diajukan' : aduan.status)}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Diajukan</td>
                        <td class="fw-medium">${aduan.created_at}</td>
                    </tr>
                    ${aduan.status === 'Selesai' && aduan.updated_at ? `
                    <tr>
                        <td class="text-muted fw-semibold">Selesai</td>
                        <td class="fw-medium">${aduan.updated_at}</td>
                    </tr>
                    ` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary mb-3 fw-semibold"><i class="fas fa-file-alt me-2"></i>Deskripsi</h6>
                <div class="p-3 bg-light rounded border" style="height: 100%; max-height: 200px; overflow-y: auto;">
                    <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">${aduan.deskripsi}</p>
                </div>
            </div>
        </div>
        ${saranPetugasHTML}
        ${aduan.foto ? `
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="text-primary mb-3 fw-semibold"><i class="fas fa-camera me-2"></i>Bukti Foto</h6>
                <div class="text-center p-3 bg-light rounded border">
                    <img src="/storage/${aduan.foto}"
                         alt="Bukti pengaduan"
                         class="img-fluid rounded"
                         style="max-height: 300px; max-width: 100%; cursor: zoom-in;"
                         onclick="if(this.style.maxHeight === '300px'){this.style.maxHeight='none'; this.style.cursor='zoom-out';} else {this.style.maxHeight='300px'; this.style.cursor='zoom-in';}">
                </div>
                <small class="d-block text-center text-muted mt-2">Klik foto untuk memperbesar/memperkecil</small>
            </div>
        </div>
        ` : ''}
        ${aduan.foto_penyelesaian ? `
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="text-success mb-3 fw-semibold"><i class="fas fa-check-circle me-2"></i>Foto Penyelesaian</h6>
                <div class="text-center p-3 bg-light rounded border">
                    <img src="/storage/${aduan.foto_penyelesaian}"
                         alt="Bukti penyelesaian"
                         class="img-fluid rounded"
                         style="max-height: 300px; max-width: 100%; cursor: zoom-in;"
                         onclick="if(this.style.maxHeight === '300px'){this.style.maxHeight='none'; this.style.cursor='zoom-out';} else {this.style.maxHeight='300px'; this.style.cursor='zoom-in';}">
                </div>
                <small class="d-block text-center text-muted mt-2">Bukti penyelesaian dari petugas</small>
            </div>
        </div>
        ` : ''}
    `;

    document.getElementById('modalBody').innerHTML = modalHTML;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

// Animasi
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
@endsection
