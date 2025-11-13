@extends('layouts.user')

@section('title', 'Ajukan Aduan')

@section('content')
<div class="content-container">
    <!-- Form Card -->
    <div class="card card-custom animate-card">
        <div class="card-body">
            <!-- Info Alert -->
            <div class="alert alert-info-custom mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fs-5 text-primary"></i>
                    <div>
                        <h6 class="mb-1 text-dark">Tips Pengisian Form</h6>
                        <p class="mb-0 text-muted">Isi form dengan detail dan jelas untuk memudahkan tim teknisi memahami masalah yang terjadi.</p>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                        <div>
                            <h6 class="mb-1">Terjadi Kesalahan</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('user.aduan.store') }}" enctype="multipart/form-data" id="aduanForm">
                @csrf

                <!-- Section: Informasi Aduan -->
                <div class="form-section mb-4">
                    <h5 class="section-title text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Aduan
                    </h5>

                    <!-- Judul Aduan -->
                    <div class="mb-4">
                        <label for="nama_pengaduan" class="form-label text-dark">
                            <i class="fas fa-heading me-2 text-primary"></i>
                            Judul Aduan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control-custom @error('nama_pengaduan') is-invalid @enderror"
                               id="nama_pengaduan" name="nama_pengaduan" value="{{ old('nama_pengaduan') }}"
                               placeholder="Contoh: Proyektor tidak menyala di ruang kelas"
                               maxlength="100" required>
                        <div class="character-count">
                            <span id="judulCount">0</span>/100 karakter
                        </div>
                        @error('nama_pengaduan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label text-dark">
                            <i class="fas fa-align-left me-2 text-primary"></i>
                            Deskripsi Masalah <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control-custom @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Jelaskan secara detail masalah yang terjadi, kapan mulai terjadi, dan dampaknya..."
                                  maxlength="500" required>{{ old('deskripsi') }}</textarea>
                        <div class="character-count">
                            <span id="deskripsiCount">0</span>/500 karakter
                        </div>
                        @error('deskripsi')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Section: Lokasi & Item -->
                <div class="form-section mb-4">
                    <h5 class="section-title text-primary mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Lokasi & Item
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <!-- Lokasi -->
                            <div class="mb-3">
                                <label for="lokasi" class="form-label text-dark">
                                    <i class="fas fa-location-dot me-2 text-primary"></i>
                                    Lokasi <span class="text-danger">*</span>
                                </label>
                                <select class="form-control-custom @error('lokasi') is-invalid @enderror"
                                        id="lokasi" name="lokasi" required>
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach($lokasiList as $namaLokasi)
                                        <option value="{{ $namaLokasi }}" {{ old('lokasi') == $namaLokasi ? 'selected' : '' }}>
                                            {{ $namaLokasi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lokasi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Item Existing -->
                            <div class="mb-3">
                                <label for="id_item" class="form-label text-dark">
                                    <i class="fas fa-cube me-2 text-primary"></i>
                                    Pilih Item yang Ada
                                </label>
                                <select name="id_item" class="form-control-custom" id="id_item" disabled>
                                    <option value="">-- Pilih Lokasi Terlebih Dahulu --</option>
                                </select>
                                <div class="form-text">Pilih item dari daftar yang tersedia</div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi: Item yang Belum Ada -->
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="request_item_baru" name="request_item_baru" value="1" {{ old('request_item_baru') ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" for="request_item_baru">
                                <i class="fas fa-plus-circle me-2 text-primary"></i>
                                Item yang Belum Ada pada Data (jika item tidak ada dalam daftar)
                            </label>
                        </div>
                    </div>

                    <!-- Section: Item yang Belum Ada -->
                    <div class="form-section mt-3" id="itemBaruSection" style="display: none;">
                        <h6 class="section-title text-warning mb-3">
                            <i class="fas fa-plus-circle me-2"></i>
                            Form Item yang Belum Ada pada Data
                        </h6>

                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 text-warning"></i>
                                <div>
                                    <h6 class="mb-1 text-dark">Item yang Belum Ada pada Data</h6>
                                    <p class="mb-0 text-muted">Item yang Anda ajukan akan ditinjau oleh admin sebelum ditambahkan ke sistem dan terhubung dengan lokasi yang dipilih.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="item_baru" class="form-label text-dark">
                                <i class="fas fa-cube me-2 text-primary"></i>
                                Nama Item yang Belum Ada <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control-custom @error('item_baru') is-invalid @enderror"
                                   id="item_baru" name="item_baru" value="{{ old('item_baru') }}"
                                   placeholder="Masukkan nama item yang belum ada pada data"
                                   maxlength="200">
                            <div class="character-count">
                                <span id="itemBaruCount">0</span>/200 karakter
                            </div>
                            @error('item_baru')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Bukti Foto -->
                <div class="form-section mb-4">
                    <h5 class="section-title text-primary mb-3">
                        <i class="fas fa-camera me-2"></i>
                        Bukti Foto
                    </h5>

                    <div class="mb-4">
                        <label class="form-label text-dark">
                            <i class="fas fa-image me-2 text-primary"></i>
                            Upload Foto (opsional)
                        </label>

                        <div class="file-upload-area" onclick="document.getElementById('foto').click()">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h6 class="text-dark mb-2">Klik untuk upload foto</h6>
                            <p class="file-info">Format: JPG, PNG, JPEG (Maks: 2MB)</p>
                            <small class="text-muted">Foto akan membantu tim teknisi memahami masalah</small>
                        </div>

                        <input type="file" class="d-none" id="foto" name="foto" accept="image/*">
                        <div id="filePreview" class="mt-3 text-center"></div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-3 mt-4 pt-3 border-top">

                    <button type="submit" class="btn btn-submit flex-fill">
                        <i class="fas fa-paper-plane me-2"></i>
                        Kirim Aduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Form Controls */
.form-control-custom {
    background: var(--light-card);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    color: var(--text-primary);
    width: 100%;
}

.form-control-custom:focus {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    color: var(--text-primary);
    outline: none;
}

.form-control-custom::placeholder {
    color: var(--text-muted);
}

/* Form Sections */
.form-section {
    padding: 1.5rem;
    background: var(--light-bg);
    border-radius: 12px;
    border: 1px solid var(--border);
    transition: all 0.3s ease;
}

.form-section:hover {
    box-shadow: var(--shadow);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--primary);
    margin-bottom: 1.5rem;
    color: var(--text-primary);
}

/* Form Labels */
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

/* Alert Custom */
.alert-info-custom {
    background: var(--primary-50);
    border: 1px solid var(--primary-light);
    border-left: 4px solid var(--primary);
    border-radius: 12px;
    padding: 1rem 1.25rem;
}

.alert-danger {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 4px solid #ef4444;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #dc2626;
}

.alert-danger h6 {
    color: #dc2626;
    font-weight: 600;
}

.alert-danger ul {
    margin-bottom: 0;
}

/* File Upload */
.file-upload-area {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
}

.file-upload-area:hover {
    border-color: var(--primary);
    background: var(--primary-50);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.file-upload-icon {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.file-info {
    color: var(--text-muted);
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

/* Buttons */
.btn-submit {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: white;
    box-shadow: var(--shadow);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.btn-cancel {
    background: transparent;
    border: 2px solid var(--border);
    color: var(--text-secondary);
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-cancel:hover {
    background: var(--light-bg);
    border-color: var(--text-muted);
    color: var(--text-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

/* Character Count */
.character-count {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-align: right;
    margin-top: 0.25rem;
}

/* Error States */
.is-invalid {
    border-color: #ef4444 !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.text-danger {
    color: #ef4444 !important;
    font-weight: 500;
}

/* Image Preview */
.img-thumbnail {
    border: 2px solid var(--primary) !important;
    border-radius: 12px !important;
    box-shadow: var(--shadow);
}

/* Responsive */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
    }

    .file-upload-area {
        padding: 1.5rem;
    }

    .btn-submit,
    .btn-cancel {
        padding: 10px 20px;
        font-size: 0.9rem;
    }

    .d-flex.gap-3 {
        flex-direction: column;
        gap: 1rem !important;
    }

    .flex-fill {
        width: 100%;
    }
}

/* Animation for file upload area */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.file-upload-area.dragover {
    animation: pulse 0.6s ease-in-out;
    border-color: var(--primary);
    background: var(--primary-50);
}

/* Select dropdown styling */
.form-control-custom select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Checkbox styling */
.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.15em;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-label {
    font-weight: 500;
    cursor: pointer;
}
</style>

<script>
// Character counters
document.getElementById('nama_pengaduan').addEventListener('input', function() {
    document.getElementById('judulCount').textContent = this.value.length;
});

document.getElementById('deskripsi').addEventListener('input', function() {
    document.getElementById('deskripsiCount').textContent = this.value.length;
});

document.getElementById('item_baru').addEventListener('input', function() {
    document.getElementById('itemBaruCount').textContent = this.value.length;
});

// Toggle section item yang belum ada
document.getElementById('request_item_baru').addEventListener('change', function() {
    const itemBaruSection = document.getElementById('itemBaruSection');
    const itemSelect = document.getElementById('id_item');

    if (this.checked) {
        itemBaruSection.style.display = 'block';
        itemSelect.disabled = true;
        itemSelect.value = '';
    } else {
        itemBaruSection.style.display = 'none';
        itemSelect.disabled = false;
        document.getElementById('item_baru').value = '';
        document.getElementById('itemBaruCount').textContent = '0';
    }
});

// Saat item existing dipilih, nonaktifkan item yang belum ada
document.getElementById('id_item').addEventListener('change', function() {
    const requestCheckbox = document.getElementById('request_item_baru');

    if (this.value !== '') {
        requestCheckbox.checked = false;
        document.getElementById('itemBaruSection').style.display = 'none';
    }
});

// Filter items berdasarkan lokasi
document.getElementById('lokasi').addEventListener('change', function() {
    const selectedLokasi = this.value;
    const itemSelect = document.getElementById('id_item');
    const requestCheckbox = document.getElementById('request_item_baru');
    const itemBaruSection = document.getElementById('itemBaruSection');

    if (selectedLokasi) {
        itemSelect.innerHTML = '<option value="">Memuat item...</option>';
        itemSelect.disabled = true;

        // Reset item yang belum ada
        requestCheckbox.checked = false;
        itemBaruSection.style.display = 'none';
        document.getElementById('item_baru').value = '';
        document.getElementById('itemBaruCount').textContent = '0';

        fetch(`{{ route('user.get.items.by.lokasi') }}?lokasi=${encodeURIComponent(selectedLokasi)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_item;
                        option.textContent = item.nama_item;
                        itemSelect.appendChild(option);
                    });
                    itemSelect.disabled = false;
                } else {
                    itemSelect.innerHTML = '<option value="">Tidak ada item di lokasi ini</option>';
                    itemSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                itemSelect.innerHTML = '<option value="">Error memuat item</option>';
                itemSelect.disabled = true;
            });
    } else {
        itemSelect.innerHTML = '<option value="">-- Pilih Lokasi Terlebih Dahulu --</option>';
        itemSelect.disabled = true;

        // Reset item yang belum ada
        requestCheckbox.checked = false;
        itemBaruSection.style.display = 'none';
        document.getElementById('item_baru').value = '';
        document.getElementById('itemBaruCount').textContent = '0';
    }
});

// File upload functionality
const fileUploadArea = document.querySelector('.file-upload-area');
const fileInput = document.getElementById('foto');
const preview = document.getElementById('filePreview');

fileUploadArea.addEventListener('click', () => fileInput.click());

fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.classList.add('dragover');
});

fileUploadArea.addEventListener('dragleave', () => {
    fileUploadArea.classList.remove('dragover');
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFile(e.target.files[0]);
    }
});

function handleFile(file) {
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            showAlert('File terlalu besar! Maksimal 2MB.', 'error');
            fileInput.value = '';
            preview.innerHTML = '';
            return;
        }

        if (!file.type.startsWith('image/')) {
            showAlert('File harus berupa gambar!', 'error');
            fileInput.value = '';
            preview.innerHTML = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="d-inline-block position-relative">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" onclick="removeImage()" style="width: 30px; height: 30px; padding: 0; transform: translate(30%, -30%);">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">${file.name}</small>
                    </div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    fileInput.value = '';
    preview.innerHTML = '';
}

function showAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-3"></i>
            <div>${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.querySelector('.card-body').insertBefore(alert, document.querySelector('form'));
    setTimeout(() => alert.remove(), 5000);
}

// Form validation
document.getElementById('aduanForm').addEventListener('submit', function(e) {
    const judul = document.getElementById('nama_pengaduan').value.trim();
    const deskripsi = document.getElementById('deskripsi').value.trim();
    const lokasi = document.getElementById('lokasi').value.trim();
    const requestItemBaru = document.getElementById('request_item_baru').checked;
    const itemBaru = document.getElementById('item_baru').value.trim();
    const idItem = document.getElementById('id_item').value;

    let isValid = true;
    let errorMessage = '';

    if (!judul) {
        isValid = false;
        errorMessage = 'Judul aduan harus diisi!';
    } else if (!deskripsi) {
        isValid = false;
        errorMessage = 'Deskripsi masalah harus diisi!';
    } else if (!lokasi) {
        isValid = false;
        errorMessage = 'Lokasi harus diisi!';
    } else if (!idItem && !requestItemBaru) {
        isValid = false;
        errorMessage = 'Silakan pilih item yang ada atau ajukan item yang belum ada pada data!';
    } else if (requestItemBaru && !itemBaru) {
        isValid = false;
        errorMessage = 'Nama item yang belum ada harus diisi!';
    }

    if (!isValid) {
        e.preventDefault();
        showAlert(errorMessage, 'error');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    // Show loading state
    const submitBtn = this.querySelector('.btn-submit');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    submitBtn.disabled = true;

    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 10000);
});

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Initialize counts
    document.getElementById('judulCount').textContent = document.getElementById('nama_pengaduan').value.length;
    document.getElementById('deskripsiCount').textContent = document.getElementById('deskripsi').value.length;
    document.getElementById('itemBaruCount').textContent = document.getElementById('item_baru').value.length;

    // Check if there's old data for request_item_baru
    const oldRequestItemBaru = {{ old('request_item_baru', 0) }};
    if (oldRequestItemBaru) {
        document.getElementById('request_item_baru').checked = true;
        document.getElementById('itemBaruSection').style.display = 'block';
        document.getElementById('id_item').disabled = true;
    }

    // Trigger change event for lokasi if has old value
    const oldLokasi = document.getElementById('lokasi').value;
    if (oldLokasi) {
        document.getElementById('lokasi').dispatchEvent(new Event('change'));
    }

    // Set old item value after items are loaded
    const oldItemId = "{{ old('id_item') }}";
    if (oldItemId && oldLokasi) {
        setTimeout(() => {
            const itemSelect = document.getElementById('id_item');
            itemSelect.value = oldItemId;
        }, 1000);
    }

    // Add animation
    const cards = document.querySelectorAll('.animate-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Auto-focus on first field
    document.getElementById('nama_pengaduan').focus();
});
</script>
@endsection
