<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['user', 'item', 'petugas']);

        // Filter pencarian
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_pengaduan', 'like', $searchTerm)
                  ->orWhere('lokasi', 'like', $searchTerm)
                  ->orWhereHas('user', function ($q_user) use ($searchTerm) {
                      $q_user->where('nama_pengguna', 'like', $searchTerm);
                  });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tgl_awal')) {
            $query->whereDate('tgl_pengajuan', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tgl_pengajuan', '<=', $request->tgl_akhir);
        }

        $pengaduan = $query->orderBy('tgl_pengajuan', 'desc')->get();

        return view('admin.manajemen_pengaduan.index', compact('pengaduan'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['user', 'item', 'petugas'])->findOrFail($id);
        $petugasList = Petugas::all();

        return view('admin.manajemen_pengaduan.show', compact('pengaduan', 'petugasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Admin hanya boleh mengubah pengaduan yang masih Diajukan
        if (!$pengaduan->canBeUpdatedByAdmin()) {
            return redirect()
                ->route('admin.pengaduan.show', $pengaduan->id_pengaduan)
                ->with('error', 'Pengaduan tidak dapat diupdate pada status ini.');
        }

        // Admin hanya boleh menetapkan Disetujui atau Ditolak
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'id_petugas' => 'nullable|exists:petugas,id_petugas',
            'saran_petugas' => 'nullable|string|max:500',
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->filled('id_petugas')) {
            $data['id_petugas'] = $request->id_petugas;
        }

        if ($request->filled('saran_petugas')) {
            $data['saran_petugas'] = $request->saran_petugas;
        }

        // Admin tidak mengatur tgl_selesai pada alur ini

        $pengaduan->update($data);

        return redirect()->route('admin.pengaduan.show', $pengaduan->id_pengaduan)
                         ->with('success', 'Pengaduan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Hapus foto jika ada
        if ($pengaduan->foto && Storage::disk('public')->exists($pengaduan->foto)) {
            Storage::disk('public')->delete($pengaduan->foto);
        }
        if ($pengaduan->foto_penyelesaian && Storage::disk('public')->exists($pengaduan->foto_penyelesaian)) {
            Storage::disk('public')->delete($pengaduan->foto_penyelesaian);
        }

        $pengaduan->delete();

        return redirect()->route('admin.pengaduan.index')
                         ->with('success', 'Pengaduan berhasil dihapus');
    }
}
