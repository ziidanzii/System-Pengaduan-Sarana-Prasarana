<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaduan;
use App\Models\User;
use App\Models\Petugas;
use App\Models\Item;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = strtolower($user->role);

        if ($role === 'administrator' || $role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'petugas') {
            return redirect()->route('petugas.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function userDashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $aduanList = Pengaduan::where('id_user', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

        // Hitung statistik
        $stats = [
            'total_aduan'    => Pengaduan::where('id_user', $user->id)->count(),
            // Tampilkan 'Disetujui' sebagai 'Diajukan' untuk pengguna
            'aduan_diajukan' => Pengaduan::where('id_user', $user->id)->whereIn('status', ['Diajukan', 'Disetujui'])->count(),
            'aduan_diproses' => Pengaduan::where('id_user', $user->id)->where('status', 'Diproses')->count(),
            'aduan_selesai'  => Pengaduan::where('id_user', $user->id)->where('status', 'Selesai')->count(),
            'aduan_ditolak'  => Pengaduan::where('id_user', $user->id)->where('status', 'Ditolak')->count(),
        ];

        return view('pengguna.dashboard.index', compact('user', 'aduanList', 'stats'));
    }

    public function admin()
    {
        // Data statistik utama
        $totalPengaduan = Pengaduan::count();
        $pengaduanSelesai = Pengaduan::where('status', 'Selesai')->count();
        $pengaduanDiproses = Pengaduan::where('status', 'Diproses')->count();
        $pengaduanDiajukan = Pengaduan::where('status', 'Diajukan')->count();
        $pengaduanDisetujui = Pengaduan::where('status', 'Disetujui')->count();
        $pengaduanDitolak = Pengaduan::where('status', 'Ditolak')->count();

        $totalUser = User::where('role', 'pengguna')->count();
        $totalPetugas = Petugas::count();
        $totalItem = Item::count();

        // Data untuk grafik tren 7 hari terakhir
        $last7Days = [];
        $last7DaysData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = $date->format('d M'); // Format: 01 Jan
            $last7DaysData[] = Pengaduan::whereDate('created_at', $date)->count();
        }

        // Data untuk grafik distribusi status (opsional)
        $statusDistribution = [
            'Selesai' => $pengaduanSelesai,
            'Diproses' => $pengaduanDiproses,
            'Diajukan' => $pengaduanDiajukan,
            'Ditolak' => $pengaduanDitolak
        ];

        // Pengaduan terbaru
        $recentPengaduan = Pengaduan::with(['user', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard_admin', compact(
            'totalPengaduan',
            'pengaduanSelesai',
            'pengaduanDiproses',
            'pengaduanDiajukan',
            'pengaduanDisetujui',
            'pengaduanDitolak',
            'totalUser',
            'totalPetugas',
            'totalItem',
            'recentPengaduan',
            'last7Days',
            'last7DaysData',
            'statusDistribution'
        ));
    }

    // Opsional: Method untuk data bulanan jika masih diperlukan
    private function getMonthlyPengaduanData()
    {
        return [
            'weeks' => ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            'pengaduan_masuk' => [12, 19, 15, 17],
            'pengaduan_selesai' => [8, 12, 10, 14]
        ];
    }

    public function userProfile()
    {
        /** @var User $user */
        $user = Auth::user();

        // Hitung statistik aduan user
        $stats = [
            'total'    => Pengaduan::where('id_user', $user->id)->count(),
            'diajukan' => Pengaduan::where('id_user', $user->id)->where('status', 'Diajukan')->count(),
            'diproses' => Pengaduan::where('id_user', $user->id)->where('status', 'Diproses')->count(),
            'selesai'  => Pengaduan::where('id_user', $user->id)->where('status', 'Selesai')->count(),
            'ditolak'  => Pengaduan::where('id_user', $user->id)->where('status', 'Ditolak')->count(),
        ];

        return view('pengguna.dashboard.profile', compact('user', 'stats'));
    }

    public function editProfile()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('pengguna.dashboard.edit-profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'nama_pengguna' => 'required|string|max:200',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->nama_pengguna = $request->nama_pengguna;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
