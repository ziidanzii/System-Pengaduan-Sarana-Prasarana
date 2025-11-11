<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengaduan::query()
            ->where(function ($q) {
                $q->where('status', '!=', 'Diajukan')
                  ->orWhereNotNull('saran_petugas');
            })
            ->orderBy('tgl_pengajuan', 'desc');

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->tanggal) {
            $query->whereDate('tgl_pengajuan', $request->tanggal);
        }

        $riwayat = $query->get();

        return view('petugas.riwayat.index', compact('riwayat'));
    }
}
