@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3>Edit Item</h3>

        <form action="{{ route('admin.items.update', $item->id_item) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label>Nama Item</label>
                <input type="text" name="nama_item" value="{{ $item->nama_item }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control">{{ $item->deskripsi }}</textarea>
            </div>
            <div class="mb-3">
                <label>Foto</label>
                @if($item->foto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$item->foto) }}" width="100">
                    </div>
                @endif
                <input type="file" name="foto" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
