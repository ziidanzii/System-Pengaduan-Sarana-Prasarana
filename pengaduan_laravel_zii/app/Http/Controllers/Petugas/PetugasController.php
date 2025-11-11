<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    // Tampilkan semua petugas
    public function index()
    {
        $petugas = Petugas::with('user')->get();
        return view('admin.manajemen_petugas.index', compact('petugas'));
    }


public function create()
{
    // CARA YANG BENAR: Tampilkan SEMUA user dengan role petugas
    $petugasUsers = User::where('role', 'petugas')->get();

    return view('admin.manajemen_petugas.create', compact('petugasUsers'));
}
public function store(Request $request)
{
    // DEBUG: Lihat data yang dikirim
    // dd($request->all());

    $request->validate([
        'id_user' => 'required|exists:users,id',
        'nama' => 'required|string|max:200',
        'gender' => 'required|in:P,L',
        'telp' => 'required|string|max:30',
    ]);

    try {
        // Cek apakah user sudah punya data petugas
        $existing = Petugas::where('id_user', $request->id_user)->exists();
        if ($existing) {
            return redirect()->back()
                ->with('error', 'User ini sudah memiliki data petugas.')
                ->withInput();
        }

        // Buat data petugas
        Petugas::create([
            'nama' => $request->nama,
            'gender' => $request->gender,
            'telp' => $request->telp,
            'id_user' => $request->id_user,
        ]);

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Data petugas berhasil ditambahkan!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}
public function createComplete()
{
    return view('admin.manajemen_petugas.create-complete');
}

public function storeComplete(Request $request)
{
    // Validasi semua field user + petugas
    $request->validate([
        // User fields
        'username' => 'required|unique:users',
        'password' => 'required|min:6',
        'email' => 'required|email|unique:users',
        // Petugas fields
        'nama' => 'required',
        'gender' => 'required|in:P,L',
        'telp' => 'required',
    ]);

    // Buat user dulu
    $user = User::create([
        'nama_pengguna' => $request->nama,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'petugas',
    ]);

    // Lalu buat data petugas
    Petugas::create([
        'nama' => $request->nama,
        'gender' => $request->gender,
        'telp' => $request->telp,
        'id_user' => $user->id,
    ]);

    return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dibuat lengkap!');
}
    // Form edit petugas
    public function edit($id)
    {
        $petugas = Petugas::with('user')->findOrFail($id);
        return view('admin.manajemen_petugas.edit', compact('petugas'));
    }

    // Update petugas
    public function update(Request $request, $id)
{
    $petugas = Petugas::with('user')->findOrFail($id);

    $request->validate([
        'nama' => 'required|string|max:200',
        'gender' => 'required|in:P,L',
        'telp' => 'required|string|max:30',
        'username' => 'required|unique:users,username,'.$petugas->id_user,
        'password' => 'nullable|min:6|confirmed',
    ]);

    // Update user
    $user = $petugas->user;
    $user->username = $request->username;
    $user->nama_pengguna = $request->nama;
    if ($request->password) {
        $user->password = Hash::make($request->password);
    }
    $user->save();

    // Update petugas
    $petugas->nama = $request->nama;
    $petugas->gender = $request->gender;
    $petugas->telp = $request->telp;
    $petugas->save();

    return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil diperbarui!');
}


    // Hapus petugas
    public function destroy(Petugas $petugas)
    {
        // Hapus user terkait
        $petugas->user()->delete();

        // Hapus petugas
        $petugas->delete();

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus!');
    }
}
