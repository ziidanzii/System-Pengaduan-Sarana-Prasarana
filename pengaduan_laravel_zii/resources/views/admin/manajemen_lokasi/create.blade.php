@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Lokasi</h2>
    <form action="{{ route('admin.lokasi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        </form>
</div>
@endsection
