@extends('layouts.app')

@section('title', 'Detail Item')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">📦 {{ $item->nama_item }}</h3>
                <p class="text-muted mb-0">Detail informasi item sarana prasarana</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.items.edit', $item->id_item) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Item
                </a>
                <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        @if($item->foto)
                            <img src="{{ asset('storage/'.$item->foto) }}"
                                 alt="{{ $item->nama_item }}"
                                 class="img-fluid rounded shadow-sm mb-3"
                                 style="max-height: 260px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                 style="height: 260px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h5 class="fw-bold text-dark mb-1">{{ $item->nama_item }}</h5>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            ID: {{ $item->id_item }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📝 Deskripsi Item</h5>
                    </div>
                    <div class="card-body">
                        @if($item->deskripsi)
                            <p class="mb-0">{{ $item->deskripsi }}</p>
                        @else
                            <p class="text-muted mb-0 fst-italic">Belum ada deskripsi untuk item ini.</p>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📍 Lokasi Terkait</h5>
                        <span class="badge bg-light text-dark">
                            {{ $item->lokasis->count() }} lokasi
                        </span>
                    </div>
                    <div class="card-body">
                        @if($item->lokasis->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($item->lokasis as $lokasi)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold text-dark">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $lokasi->nama_lokasi }}
                                        </span>
                                        <a href="{{ route('admin.lokasi.show', $lokasi->id_lokasi) }}" class="btn btn-sm btn-outline-primary">
                                            Lihat Lokasi
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0 fst-italic">Item ini belum terhubung dengan lokasi manapun.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush
@endsection

