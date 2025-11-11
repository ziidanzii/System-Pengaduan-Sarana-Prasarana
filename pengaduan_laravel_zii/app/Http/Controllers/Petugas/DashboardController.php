<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik
        $pengaduan = Pengaduan::whereIn('status', ['Diajukan', 'Disetujui', 'Diproses'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $jumlahDiajukan = Pengaduan::where('status', 'Diajukan')->count();
        $jumlahDiproses = Pengaduan::where('status', 'Diproses')->count();
        $jumlahSelesai = Pengaduan::where('status', 'Selesai')
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalDitangani = Pengaduan::whereIn('status', ['Diproses', 'Selesai'])
            ->whereMonth('created_at', now()->month)
            ->count();

        // Data untuk grafik
        $trendLabels = [];
        $trendDataMasuk = [];
        $trendDataSelesai = [];

        // Generate data 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('D');

            $masuk = Pengaduan::whereDate('created_at', $date)->count();
            $selesai = Pengaduan::whereDate('tgl_selesai', $date)->count();

            $trendDataMasuk[] = $masuk;
            $trendDataSelesai[] = $selesai;
        }

        // Performance metrics
        $totalCompleted = Pengaduan::where('status', 'Selesai')->count();
        $totalAssigned = Pengaduan::whereIn('status', ['Diproses', 'Selesai'])->count();
        $completionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;

        $responseRate = 85; // Contoh statis, bisa dihitung dari database
        $avgResponseTime = 4; // Contoh dalam jam

        return view('petugas.dashboard', compact(
            'pengaduan',
            'jumlahDiajukan',
            'jumlahDiproses',
            'jumlahSelesai',
            'totalDitangani',
            'trendLabels',
            'trendDataMasuk',
            'trendDataSelesai',
            'completionRate',
            'responseRate',
            'avgResponseTime'
        ));
    }
}
