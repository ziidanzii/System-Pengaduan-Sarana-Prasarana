@extends('layouts.petugas')

@section('title', 'Detail Pengaduan')

@section('content')
<div class="content-container">
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2 text-primary fw-bold">Detail Pengaduan</h2>
                    <p class="text-muted mb-0">Kelola status dan berikan saran untuk pengaduan ini</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-info-circle me-2"></i>ID: {{ $pengaduan->id_pengaduan }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom animate-card mb-4" style="animation-delay: 0.1s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-clipboard-list me-2"></i>Informasi Pengaduan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small mb-1">Judul Pengaduan</label>
                            <div class="fw-semibold text-dark fs-5">{{ $pengaduan->nama_pengaduan }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small mb-1">Status</label>
                            <div>
                                <span class="complaint-status status-{{ strtolower($pengaduan->status) }}">
                                    {{ $pengaduan->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                            </label>
                            <div class="fw-medium text-dark">{{ $pengaduan->lokasi }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="fas fa-user me-1"></i>Pengaju
                            </label>
                            <div class="fw-medium text-dark">{{ $pengaduan->user->nama_pengguna ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">
                            <i class="fas fa-align-left me-1"></i>Deskripsi
                        </label>
                        <div class="p-3 bg-light rounded">
                            {{ $pengaduan->deskripsi ?? 'Tidak ada deskripsi' }}
                        </div>
                    </div>

                    @if($pengaduan->foto)
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">
                            <i class="fas fa-camera me-1"></i>Foto Bukti
                        </label>
                        <div class="text-center">
                            <img src="{{ $pengaduan->foto_url }}"
                                    alt="Foto Pengaduan"
                                    class="img-fluid rounded shadow-sm"
                                    style="max-width: 400px; cursor: pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal">
                            <p class="text-muted small mt-2">Klik gambar untuk memperbesar</p>
                        </div>
                    </div>
                    @endif

                    @if($pengaduan->foto_penyelesaian)
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">
                            <i class="fas fa-check-circle me-1"></i>Foto Penyelesaian
                        </label>
                        <div class="text-center">
                            <img src="{{ $pengaduan->foto_penyelesaian_url }}"
                                 alt="Foto Penyelesaian"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-width: 400px; cursor: pointer;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#completionImageModal">
                            <p class="text-muted small mt-2">Bukti penyelesaian yang telah diunggah</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom animate-card mb-4" style="animation-delay: 0.2s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-success fw-bold">
                        <i class="fas fa-edit me-2"></i>Update Status
                    </h5>
                </div>
                <div class="card-body">
                    @if(in_array($pengaduan->status, ['Disetujui','Diproses']))
                    <form method="POST"
                          action="{{ route('petugas.pengaduan.update', $pengaduan->id_pengaduan) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Pengaduan</label>
                            <select name="status" class="form-select status-select" required>
                                <option value="Diproses" {{ $pengaduan->status == 'Diproses' ? 'selected' : '' }} data-color="warning">Diproses</option>
                                <option value="Selesai" {{ $pengaduan->status == 'Selesai' ? 'selected' : '' }} data-color="success">Selesai</option>
                            </select>
                            <div class="form-text text-muted">
                                <strong>Alur Kerja:</strong>
                                <ul class="mb-0 mt-1">
                                    <li><strong>Diproses:</strong> Sedang menangani pengaduan.</li>
                                    <li><strong>Selesai:</strong> Pengaduan telah diselesaikan.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Saran Petugas</label>
                            <textarea name="saran_petugas"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Berikan saran atau catatan untuk pengaduan ini...">{{ $pengaduan->saran_petugas ?? '' }}</textarea>
                            <div class="form-text text-muted">
                                Saran akan ditampilkan kepada pengguna yang mengajukan pengaduan.
                            </div>
                        </div>

                        <div class="mb-4" id="completionPhotoSection" style="display: none;">
                            <label class="form-label fw-semibold">Foto Bukti Penyelesaian</label>
                            <input type="file"
                                   name="foto_penyelesaian"
                                   id="foto_penyelesaian"
                                   class="form-control @error('foto_penyelesaian') is-invalid @enderror"
                                   accept="image/*">
                            @error('foto_penyelesaian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Unggah foto sebagai bukti bahwa pekerjaan telah diselesaikan (maksimal 2MB, format JPG/PNG).
                            </div>
                            @if($pengaduan->foto_penyelesaian)
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Foto bukti penyelesaian sudah tersedia. Unggah file baru jika ingin menggantinya.
                                </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Update Pengaduan
                            </button>

                        </div>
                    </form>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pengaduan dengan status <strong>{{ $pengaduan->status }}</strong> tidak dapat diubah.
                    </div>
                    @endif
                </div>
            </div>

            <div class="card card-custom animate-card" style="animation-delay: 0.3s">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-clock me-2"></i>Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <small class="text-muted">Diajukan pada</small>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($pengaduan->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @if($pengaduan->updated_at != $pengaduan->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">Terakhir diupdate</small>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($pengaduan->updated_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pengaduan->foto)
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Pengaduan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $pengaduan->foto_url }}"
                    alt="Foto Pengaduan"
                    class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endif

@if($pengaduan->foto_penyelesaian)
<div class="modal fade" id="completionImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Penyelesaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $pengaduan->foto_penyelesaian_url }}"
                    alt="Foto Penyelesaian"
                    class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endif

<style>
    /* Complaint Status */
    .complaint-status {
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgb(255, 255, 255);
        box-shadow: var(--shadow);
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }

    /* --- Perubahan pada CSS di bawah ini --- */

    /* Status Menunggu (Jika ada) */
    .status-diajukan {
    /* Gradien Abu-abu Netral / Biru Muda (Info) */
    background: linear-gradient(135deg, #60a5fa, #3b82f6); /* Warna Biru Soft (Info/Diajukan) */
    /* Jika Anda ingin Abu-abu: background: linear-gradient(135deg, #9ca3af, #6b7280); */
}

    /* Status Diproses (diubah menjadi abu-abu, atau tetap warning) */
    .status-diproses {
        /* Mengubahnya menjadi abu-abu netral */
        background: linear-gradient(135deg, #a1a1aa, #71717a); /* Warna Abu-abu Netral */

        /* ATAU, jika ingin mempertahankan warna kuning/oranye untuk 'Diproses': */
        /* background: linear-gradient(135deg, #f59e0b, #d97706); */
    }

    /* Status Selesai */
    .status-selesai {
        background: linear-gradient(135deg, #10b981, #059669); /* Hijau */
    }

    /* --- Akhir Perubahan CSS --- */

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 1rem;
    }

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
    padding-left: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -0.5rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content {
    margin-left: 0.5rem;
}

/* Form Styling */
.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid var(--border);
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1);
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    box-shadow: var(--shadow);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
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

/* Image Styling */
.img-fluid {
    transition: transform 0.3s ease;
}

.img-fluid:hover {
    transform: scale(1.02);
}

/* Responsive */
@media (max-width: 768px) {
    .complaint-status {
        font-size: 0.7rem;
        padding: 0.4rem 1rem;
        min-width: 100px;
    }

    .card-body {
        padding: 1rem;
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

    // Update select border color based on selected status
    const statusSelect = document.querySelector('.status-select');
    const completionSection = document.getElementById('completionPhotoSection');
    const completionInput = document.getElementById('foto_penyelesaian');
    const hasExistingCompletion = @json((bool) $pengaduan->foto_penyelesaian);

    function updateSelectColor() {
        const selectedOption = statusSelect.options[statusSelect.selectedIndex];
        const color = selectedOption.getAttribute('data-color');
        statusSelect.style.borderColor = `var(--bs-${color})`;
    }

    function toggleCompletionSection() {
        const isSelesai = statusSelect.value === 'Selesai';
        if (completionSection) {
            completionSection.style.display = isSelesai ? 'block' : 'none';
        }
        if (completionInput) {
            completionInput.required = isSelesai && !hasExistingCompletion;
        }
    }

    statusSelect.addEventListener('change', updateSelectColor);
    statusSelect.addEventListener('change', toggleCompletionSection);
    updateSelectColor(); // Initialize on load
    toggleCompletionSection();
});
</script>
@endsection
