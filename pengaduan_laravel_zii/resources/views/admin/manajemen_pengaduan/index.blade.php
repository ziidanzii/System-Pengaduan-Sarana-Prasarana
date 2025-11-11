@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="fas fa-clipboard-list me-2"></i> Manajemen Pengaduan
        </h3>
        
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif


    {{-- FILTER & PENCARIAN --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.pengaduan.index') }}" method="GET" id="filter-form">
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label for="search" class="form-label fw-semibold">🔍 Cari Pengaduan</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Cari berdasarkan judul, lokasi, atau nama pengadu..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(['Diajukan', 'Disetujui', 'Diproses', 'Selesai', 'Ditolak'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="tgl_awal" class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="tgl_awal" id="tgl_awal" class="form-control" value="{{ request('tgl_awal') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="tgl_akhir" class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control" value="{{ request('tgl_akhir') }}">
                    </div>

                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary w-100 me-2">
                            <i class="fas fa-filter me-1"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Cetak
                        </button>
                    </div>
                </div>

                @if(request('search') || request('status') || request('tgl_awal') || request('tgl_akhir'))
                    <div class="mt-3">
                        <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-undo me-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- TABEL LIST PENGADUAN (HANYA INI YANG DICETAK) --}}
    <div class="card shadow-sm border-0 print-area">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengadu</th>
                            <th>Judul Pengaduan</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tgl Pengajuan</th>
                            <th>Tgl Selesai</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($pengaduan as $key => $p)
                        <tr class="{{ in_array($p->status, ['Selesai', 'Disetujui', 'Ditolak']) ? 'table-light' : '' }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $p->user->nama_pengguna ?? '-' }}</td>
                            <td class="text-start">{{ $p->nama_pengaduan }}</td>
                            <td>{{ $p->lokasi }}</td>
                            <td>
                                @switch($p->status)
                                    @case('Selesai') <span class="badge bg-success">Selesai</span> @break
                                    @case('Diproses') <span class="badge bg-warning text-dark">Diproses</span> @break
                                    @case('Disetujui') <span class="badge bg-primary">Disetujui</span> @break
                                    @case('Ditolak') <span class="badge bg-danger">Ditolak</span> @break
                                    @default <span class="badge bg-secondary">{{ $p->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_pengajuan)->format('d-m-Y') }}</td>
                            <td>{{ $p->tgl_selesai ? \Carbon\Carbon::parse($p->tgl_selesai)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $p->petugas->nama ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.pengaduan.show', $p->id_pengaduan) }}"
                                   class="btn btn-outline-primary btn-sm">
                                   <i class="fas fa-eye me-1"></i> Detail
                                </a>
                                <form action="{{ route('admin.pengaduan.destroy', $p->id_pengaduan) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengaduan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-2"></i>Belum ada pengaduan atau tidak ditemukan hasil.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TANGGAL CETAK OTOMATIS --}}
            <div class="mt-4 text-end small text-muted">
                Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i') }}
            </div>
        </div>
    </div>
</div>
<style>
    .table td, .table th {
        vertical-align: middle;
    }

    @media print {
        /* 🔒 Sembunyikan semua elemen di luar area cetak */
        body * {
            visibility: hidden;
        }

        .print-area, .print-area * {
            visibility: visible;
        }

        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* 🧾 Judul laporan di atas tabel */
        body::before {
            content: "Laporan Pengaduan Sarana dan Prasarana Sekolah";
            display: block;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        /* 📊 Tampilan tabel cetak */
        .table {
            border-collapse: collapse !important;
            width: 100%;
        }

        /* Header tabel kontras hitam-putih */
        .table thead th {
            background-color: #000000 !important; /* hitam pekat */
            color: #ffffff !important; /* teks putih */
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            border: 1.5px solid #000 !important;
            padding: 8px;
        }

        /* Isi tabel */
        .table td {
            border: 1px solid #000 !important;
            color: #000 !important;
            padding: 6px 8px;
            font-size: 13px;
        }

        /* Baris bergantian */
        .table tbody tr:nth-child(odd) {
            background-color: #f5f5f5 !important;
        }

        /* Warna badge status tetap tampil */
        .badge {
            border-radius: 4px;
            padding: 3px 6px;
            font-size: 11px;
            font-weight: bold;
            color: #fff !important;
        }

        .bg-success { background-color: #198754 !important; }
        .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .bg-primary { background-color: #0d6efd !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-secondary { background-color: #6c757d !important; }

        /* Nonaktifkan tombol, filter, dsb. */
        button, .btn, form, .d-flex, .row.g-3, .card.shadow-sm.border-0:not(.print-area) {
            display: none !important;
        }

        /* Info teks */
        .text-muted {
            color: #000 !important;
            font-style: italic;
        }
    }
</style>

@endsection
