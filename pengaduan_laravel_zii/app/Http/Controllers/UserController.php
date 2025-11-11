<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::all();
        return view('admin.manajemen_user.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {
        return view('admin.manajemen_user.create');
    }

    // Simpan user baru
    public function store(Request $request)
{
    $request->validate([
        'nama_pengguna' => 'required|string|max:200',
        'username' => 'required|string|unique:users',
        'email' => 'required|email|unique:users', // Tambahkan validasi email
        'password' => 'required|string|min:6',
        'role' => 'required|in:administrator,petugas,pengguna',
    ]);

    User::create([
        'nama_pengguna' => $request->nama_pengguna,
        'username' => $request->username,
        'email' => $request->email, // Tambahkan email
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    return redirect()->route('admin.users.index')
                     ->with('success', 'User berhasil ditambahkan');
}

    // Form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.manajemen_user.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_pengguna' => 'required|string|max:200',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role' => 'required|in:administrator,petugas,pengguna',
            'password' => 'nullable|string|min:6|confirmed', // password optional & harus konfirmasi
        ]);

        $data = $request->only(['nama_pengguna', 'username', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil diperbarui');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil dihapus');
    }
    public function profile()
    {
        $user = Auth::user();

        // Ambil statistik aduan dari database
        $stats = [
            'total' => Pengaduan::where('user_id', $user->id)->count(),
            'diajukan' => Pengaduan::where('user_id', $user->id)->where('status', 'Diajukan')->count(),
            'diproses' => Pengaduan::where('user_id', $user->id)->where('status', 'Diproses')->count(),
            'selesai' => Pengaduan::where('user_id', $user->id)->where('status', 'Selesai')->count(),
        ];

        return view('user.profile', compact('user', 'stats'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('user.profile-edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            // tambahkan validasi lainnya sesuai kebutuhan
        ]);

        $user->update($validated);

        return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
