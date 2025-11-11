@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h3 class="fw-bold mb-1">Selamat datang, {{ Auth::user()->nama_pengguna ?? Auth::user()->name }} 👋</h3>
        <p class="text-muted">Anda login sebagai <strong>{{ ucfirst(Auth::user()->role) }}</strong></p>

        <hr class="my-4">

        {{-- STATISTIK STATUS PENGAJUAN --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📊 Status Pengaduan</h5>
                    </div>
                    <div class="card-body">
                       
                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Progress Penyelesaian Pengaduan</span>
                                <span>
                                    @if($totalPengaduan > 0)
                                        {{ round(($pengaduanSelesai / $totalPengaduan) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $totalPengaduan > 0 ? ($pengaduanSelesai / $totalPengaduan) * 100 : 0 }}%"
                                     aria-valuenow="{{ $totalPengaduan > 0 ? ($pengaduanSelesai / $totalPengaduan) * 100 : 0 }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK DISTRIBUSI STATUS --}}
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📈 Distribusi Status Pengaduan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusDistributionChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📅 Tren Pengaduan 7 Hari Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyTrendChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- PENGAJUAN TERBARU --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Pengaduan Terbaru</h5>
                        <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-sm btn-light">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Judul Pengaduan</th>
                                        <th>Pengadu</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPengaduan as $key => $p)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong class="text-dark">{{ $p->nama_pengaduan }}</strong>
                                                @if($p->deskripsi)
                                                    <small class="text-muted">{{ Str::limit($p->deskripsi, 60) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                     style="width: 32px; height: 32px;">
                                                    <span class="text-white fw-bold small">
                                                        {{ substr($p->user->nama_pengguna ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span>{{ $p->user->nama_pengguna ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                                {{ $p->lokasi }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($p->status == 'Selesai')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                                </span>
                                            @elseif($p->status == 'Diproses')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-sync-alt me-1"></i>Diproses
                                                </span>
                                            @elseif($p->status == 'Disetujui')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-thumbs-up me-1"></i>Disetujui
                                                </span>
                                            @elseif($p->status == 'Ditolak')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Ditolak
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-clock me-1"></i>{{ $p->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}<br>
                                                <span class="text-dark">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pengaduan.show', $p->id_pengaduan) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="mb-0">Belum ada pengaduan</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.7rem 1.5rem rgba(0,0,0,0.15);
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Distribusi Status
        const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Diproses', 'Diajukan', 'Ditolak'],
                datasets: [{
                    data: [
                        {{ $pengaduanSelesai ?? 0 }},
                        {{ $pengaduanDiproses ?? 0 }},
                        {{ $pengaduanDiajukan ?? 0 }},
                        {{ $pengaduanDitolak ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#17a2b8',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Grafik Tren Mingguan
        const trendCtx = document.getElementById('weeklyTrendChart').getContext('2d');
        const weeklyTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($last7Days) !!},
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: {!! json_encode($last7DaysData) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderColor: '#28a745',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
