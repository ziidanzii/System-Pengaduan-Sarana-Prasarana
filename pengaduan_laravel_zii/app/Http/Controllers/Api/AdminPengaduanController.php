<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;

class AdminPengaduanController extends Controller
{
    /**
     * GET /api/admin/pengaduan → semua pengaduan (untuk admin)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Cek apakah user adalah admin
        if ($user->role !== 'admin' && $user->role !== 'administrator') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $query = Pengaduan::with(['user', 'item', 'petugas']);

        // Filter status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pengaduan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $pengaduan = $query->orderByDesc('tgl_pengajuan')->get();

        return response()->json($pengaduan);
    }

    /**
     * GET /api/admin/pengaduan/{id} → detail pengaduan
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        // Cek apakah user adalah admin
        if ($user->role !== 'admin' && $user->role !== 'administrator') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $pengaduan = Pengaduan::with(['user', 'item', 'petugas'])
            ->findOrFail($id);

        return response()->json($pengaduan);
    }

    /**
     * PUT /api/admin/pengaduan/{id} → update status pengaduan
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        // Cek apakah user adalah admin
        if ($user->role !== 'admin' && $user->role !== 'administrator') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $pengaduan = Pengaduan::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Diajukan,Disetujui,Ditolak,Diproses,Selesai',
            'saran_petugas' => 'nullable|string|max:500',
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->filled('saran_petugas')) {
            $data['saran_petugas'] = $request->saran_petugas;
        }

        // Jika status selesai, set tanggal selesai
        if ($request->status === 'Selesai') {
            $data['tgl_selesai'] = now();
        }

        $pengaduan->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Status pengaduan berhasil diperbarui',
            'data' => $pengaduan->fresh(['user', 'item', 'petugas']),
        ]);
    }
}


