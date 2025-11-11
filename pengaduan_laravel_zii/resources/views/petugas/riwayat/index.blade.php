@extends('layouts.petugas')

@section('title', 'Riwayat Pengaduan')

@section('content')
<div class="content-container">
    <!-- Header -->
    <div class="card card-custom animate-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2 text-primary fw-bold">Riwayat Pengaduan</h2>
                    <p class="text-muted mb-0">Daftar lengkap semua pengaduan yang telah ditangani</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-history me-2"></i>Total: {{ $riwayat->count() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card card-custom mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('petugas.riwayat') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold">Filter Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tanggal" class="form-label fw-semibold">Filter Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('petugas.riwayat') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tombol Cetak -->
    <div class="text-end mb-3">
        <button onclick="printTable()" class="btn btn-success">
            <i class="fas fa-print me-1"></i> Cetak Tabel
        </button>
    </div>

    <!-- Table -->
    <div class="card card-custom animate-card" id="print-area">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Daftar Riwayat Pengaduan</h5>
        </div>
        <div class="card-body p-0">
            @if($riwayat->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-dark mb-2">Belum Ada Riwayat</h5>
                    <p class="text-muted">Tidak ada riwayat pengaduan yang ditemukan</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #000; color: #fff;">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Pengadu</th>
                                <th>Pengaduan</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="pe-4">Catatan / Saran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $item)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>{{ $item->user->nama_pengguna ?? '-' }}</td>
                                <td>
                                    <strong>{{ $item->nama_pengaduan }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small>
                                </td>
                                <td>{{ $item->lokasi }}</td>
                                <td>
                                    <span class="badge bg-{{ strtolower($item->status) == 'selesai' ? 'success' : (strtolower($item->status) == 'diproses' ? 'warning' : (strtolower($item->status) == 'ditolak' ? 'danger' : 'info')) }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d M Y, H:i') }}</td>
                                <td>{{ $item->saran_petugas ?? '-' }}</td>
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
/* Kontras Header */
.table thead th {
    font-weight: 700;
    text-transform: uppercase;
    background: #000;
    color: #fff;
}

/* Cetak hanya tabel */
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .table th, .table td {
        border: 1px solid #000 !important;
        color: #000 !important;
        font-size: 12px !important;
    }
}
</style>

<script>
function printTable() {
    window.print();
}
</script>
@endsection
