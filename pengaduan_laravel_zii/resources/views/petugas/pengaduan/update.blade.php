@extends('layouts.app')

@section('title', 'Detail Pengaduan')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm p-4">
        <h3>Detail Pengaduan</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p><strong>Pelapor:</strong> {{ $pengaduan->user->nama_pengguna ?? '-' }}</p>
        <p><strong>Nama Pengaduan:</strong> {{ $pengaduan->nama_pengaduan }}</p>
        <p><strong>Deskripsi:</strong> {{ $pengaduan->deskripsi }}</p>
        <p><strong>Status Saat Ini:</strong> {{ $pengaduan->status }}</p>
        <p><strong>Saran Petugas:</strong> {{ $pengaduan->saran_petugas ?? '-' }}</p>

        <hr>

        <form action="{{ route('petugas.pengaduan.update', $pengaduan->id_pengaduan) }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="Diproses" {{ $pengaduan->status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="Selesai" {{ $pengaduan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Saran Petugas</label>
        <textarea name="saran_petugas" class="form-control">{{ $pengaduan->saran_petugas }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Status</button>
</form>

    </div>
</div>
@endsection
