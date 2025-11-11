@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Detail & Edit Pengaduan</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pengaduan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Judul Pengaduan:</strong>
                            <p class="fs-5">{{ $pengaduan->nama_pengaduan }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <div>
                                @php $statusClass = 'status-'.strtolower($pengaduan->status); @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $pengaduan->status }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Lokasi:</strong>
                            <p>{{ $pengaduan->lokasi }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Pengaju:</strong>
                            <p>{{ $pengaduan->user->nama_pengguna ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- INFORMASI ITEM DARI PENGAJUAN -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Item yang Diadukan:</strong>
                            @if($pengaduan->id_item && $pengaduan->item)
                                <p class="text-success fw-semibold">
                                    <i class="fas fa-cube me-2"></i>{{ $pengaduan->item->nama_item }}
                                </p>
                                <small class="text-muted">ID: {{ $pengaduan->id_item }}</small>
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-cube me-2"></i>Tidak ada item terkait
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Jenis Item:</strong>
                            @if($pengaduan->id_item)
                                <p class="text-info">
                                    <i class="fas fa-check-circle me-2"></i>Item Existing
                                </p>
                            @else
                                <p class="text-warning">
                                    <i class="fas fa-clock me-2"></i>Request Item Baru
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Deskripsi Masalah:</strong>
                        <div class="p-3 bg-light rounded">
                            {{ $pengaduan->deskripsi ?? 'Tidak ada deskripsi' }}
                        </div>
                    </div>

                    @if($pengaduan->foto)
                    <div class="mb-3">
                        <strong>Foto Bukti:</strong>
                        <div class="mt-2 text-center">
                            <img src="{{ $pengaduan->foto_url }}"
                                 alt="Foto Pengaduan"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 400px; cursor: pointer;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#imageModal">
                            <p class="text-muted small mt-2">Klik gambar untuk memperbesar</p>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Tidak ada foto yang diupload
                    </div>
                    @endif

                    @if($pengaduan->foto_penyelesaian)
                    <div class="mb-3">
                        <strong>Foto Penyelesaian:</strong>
                        <div class="mt-2 text-center">
                            <img src="{{ $pengaduan->foto_penyelesaian_url }}"
                                 alt="Foto Penyelesaian"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 400px; cursor: pointer;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#completionImageModal">
                            <p class="text-muted small mt-2">Foto bukti penyelesaian dari petugas</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Form Edit Status -->
            @if($pengaduan->canBeUpdatedByAdmin())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pengaduan.update', $pengaduan->id_pengaduan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Disetujui" {{ old('status', $pengaduan->status) == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ old('status', $pengaduan->status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <strong>Perhatian:</strong> Admin hanya dapat menyetujui atau menolak pengaduan.
                                Pengaduan yang disetujui akan diproses oleh petugas.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Status Tidak Dapat Diedit</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pengaduan ini tidak dapat diedit karena status sudah <strong>{{ $pengaduan->status }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <!-- INFORMASI ITEM DARI PENGAJUAN -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cube me-2"></i>Informasi Item
                    </h5>
                </div>
                <div class="card-body">
                    @if($pengaduan->id_item && $pengaduan->item)
                        <!-- JIKA ADA ITEM EXISTING -->
                        <div class="text-center mb-3">
                            @if($pengaduan->item->foto)
                                <img src="{{ asset('storage/' . $pengaduan->item->foto) }}"
                                     alt="{{ $pengaduan->item->nama_item }}"
                                     class="img-fluid rounded mb-3"
                                     style="max-height: 120px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                     style="height: 120px;">
                                    <i class="fas fa-cube fa-2x text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama Item:</strong></td>
                                <td>{{ $pengaduan->item->nama_item }}</td>
                            </tr>
                            <tr>
                                <td><strong>ID Item:</strong></td>
                                <td><code>{{ $pengaduan->item->id_item }}</code></td>
                            </tr>
                            @if($pengaduan->item->deskripsi)
                            <tr>
                                <td><strong>Deskripsi:</strong></td>
                                <td class="small">{{ $pengaduan->item->deskripsi }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Dibuat:</strong></td>
                                <td class="small">{{ \Carbon\Carbon::parse($pengaduan->item->created_at)->format('d M Y') }}</td>
                            </tr>
                        </table>

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('admin.items.edit', $pengaduan->item->id_item) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Item
                            </a>
                            <a href="{{ route('admin.items.show', $pengaduan->item->id_item) }}"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    @else
                        <!-- JIKA TIDAK ADA ITEM EXISTING, CEK APAKAH ADA REQUEST ITEM BARU -->
                        @php
                            $temporaryItem = \App\Models\TemporaryItem::where('id_pengaduan', $pengaduan->id_pengaduan)->first();
                        @endphp

                        @if($temporaryItem)
                            <!-- JIKA ADA REQUEST ITEM BARU -->
                            <div class="alert alert-warning">
                                <h6 class="alert-heading mb-3">
                                    <i class="fas fa-clock me-1"></i>Request Item Baru
                                </h6>

                                <div class="mb-3">
                                    <strong>Nama Item Baru:</strong>
                                    <p class="mb-1 fw-semibold">{{ $temporaryItem->nama_barang_baru }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Lokasi Item:</strong>
                                    <p class="mb-1">{{ $temporaryItem->lokasi_barang_baru }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Status Request:</strong>
                                    <p class="mb-1">
                                        @if($temporaryItem->status == 'pending')
                                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                                        @elseif($temporaryItem->status == 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </p>
                                </div>

                                @if($temporaryItem->alasan_penolakan)
                                <div class="mb-3">
                                    <strong>Alasan Penolakan:</strong>
                                    <p class="mb-1 small text-danger">{{ $temporaryItem->alasan_penolakan }}</p>
                                </div>
                                @endif

                                <div class="d-grid">
                                    <a href="{{ route('admin.temp.items') }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-cog me-1"></i>Kelola Request
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- JIKA TIDAK ADA ITEM SAMA SEKALI -->
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-cube fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada item terkait</p>
                                <small>Pengaduan ini tidak memiliki item yang spesifik</small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- INFORMASI TAMBAHAN PENGAJUAN -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Informasi Tambahan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Tanggal Pengajuan:</strong>
                        <p class="mb-1">{{ \Carbon\Carbon::parse($pengaduan->tgl_pengajuan)->format('d M Y H:i') }}</p>
                    </div>

                    @if($pengaduan->tgl_selesai)
                    <div class="mb-3">
                        <strong>Tanggal Selesai:</strong>
                        <p class="mb-1">{{ \Carbon\Carbon::parse($pengaduan->tgl_selesai)->format('d M Y H:i') }}</p>
                    </div>
                    @endif

                    @if($pengaduan->petugas)
                    <div class="mb-3">
                        <strong>Petugas Penanganan:</strong>
                        <p class="mb-1">{{ $pengaduan->petugas->nama ?? '-' }}</p>
                    </div>
                    @endif

                    @if($pengaduan->saran_petugas)
                    <div class="mb-3">
                        <strong>Saran Petugas:</strong>
                        <div class="p-2 bg-light rounded small">
                            {{ $pengaduan->saran_petugas }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
@if($pengaduan->foto)
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Pengaduan - {{ $pengaduan->nama_pengaduan }}</h5>
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
                <h5 class="modal-title">Foto Penyelesaian - {{ $pengaduan->nama_pengaduan }}</h5>
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
.img-fluid {
    transition: transform 0.3s ease;
}
.img-fluid:hover {
    transform: scale(1.02);
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #198754, #157347);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #157347, #0f5132);
    transform: translateY(-1px);
}

.card-header {
    border-bottom: none;
}

.table-sm td {
    padding: 0.25rem 0;
    vertical-align: top;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status change handling
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === 'Disetujui') {
                this.style.borderColor = '#198754';
            } else if (selectedOption.value === 'Ditolak') {
                this.style.borderColor = '#dc3545';
            }
        });

        // Trigger change on load
        statusSelect.dispatchEvent(new Event('change'));
    }

    // Auto close alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection
