@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Lokasi</h2>
    <form action="{{ route('admin.lokasi.update', $lokasi->id_lokasi) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" class="form-control" value="{{ $lokasi->nama_lokasi }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
