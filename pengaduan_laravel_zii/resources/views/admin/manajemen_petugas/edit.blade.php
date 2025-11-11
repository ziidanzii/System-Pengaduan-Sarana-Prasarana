@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Petugas</h2>

    <form method="POST" action="{{ route('admin.petugas.update', $petugas->id_petugas) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $petugas->nama }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option value="L" {{ $petugas->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $petugas->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Telp</label>
            <input type="text" name="telp" value="{{ $petugas->telp }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" value="{{ $petugas->user->username }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password (biarkan kosong jika tidak diganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
