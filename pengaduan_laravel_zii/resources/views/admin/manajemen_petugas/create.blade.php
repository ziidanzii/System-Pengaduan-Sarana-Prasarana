@extends('layouts.app')

@section('title', 'Tambah Data Petugas')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">🛠️ Tambah Data Petugas</h3>
                <p class="text-muted mb-0">Lengkapi data detail petugas</p>
            </div>
            
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📝 Form Data Petugas</h5>
            </div>
            <div class="card-body">
                <!-- TAMPILKAN ERROR JIKA ADA -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- TAMPILKAN SESSION MESSAGE -->
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($petugasUsers->count() > 0)
                <form method="POST" action="{{ route('admin.petugas.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih User Petugas *</label>
                            <select name="id_user" class="form-select @error('id_user') is-invalid @enderror" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($petugasUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_pengguna }} ({{ $user->username }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih user yang sudah dibuat di Manajemen User dengan role 'petugas'</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Pilih Gender --</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon *</label>
                            <input type="text" name="telp" class="form-control @error('telp') is-invalid @enderror"
                                   value="{{ old('telp') }}" required>
                            @error('telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Simpan Data Petugas
                        </button>
                        <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </form>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada user yang tersedia</h5>
                    <p class="text-muted">Belum ada user dengan role 'petugas' yang belum memiliki data detail.</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-2"></i>Buat User Petugas
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Lihat Semua User
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
