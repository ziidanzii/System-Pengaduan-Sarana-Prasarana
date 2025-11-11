<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;

class PengaduanApiController extends Controller
{
    // GET /api/pengaduan → semua pengaduan milik user
    public function index(Request $request)
    {
        $user = $request->user();
        $pengaduan = Pengaduan::where('id_user', $user->id)
            ->orderByDesc('tgl_pengajuan')
            ->get();

        return response()->json($pengaduan);
    }

    // POST /api/pengaduan → ajukan pengaduan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengaduan' => 'required|string',
            'deskripsi' => 'required|string',
            'id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'id_item' => 'nullable|exists:items,id_item',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Ambil nama lokasi dari id_lokasi
        $lokasi = \App\Models\Lokasi::findOrFail($request->id_lokasi);
        $namaLokasi = $lokasi->nama_lokasi;

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('pengaduan', 'public');
        }

        $pengaduan = Pengaduan::create([
            'nama_pengaduan' => $request->nama_pengaduan,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $namaLokasi, // Simpan nama lokasi sebagai string
            'id_item' => $request->id_item,
            'foto' => $path,
            'id_user' => $request->user()->id,
            'status' => Pengaduan::STATUS_DIAJUKAN,
            'tgl_pengajuan' => now(),
        ]);

        return response()->json($pengaduan, 201);
    }

    // GET /api/pengaduan/{id} → detail
    public function show(Request $request, $id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', $request->user()->id)
            ->firstOrFail();

        return response()->json($pengaduan);
    }

    // (Opsional) DELETE /api/pengaduan/{id} → hapus pengaduan
    public function destroy(Request $request, $id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', $request->user()->id)
            ->firstOrFail();

        if ($pengaduan->foto) {
            Storage::disk('public')->delete($pengaduan->foto);
        }

        $pengaduan->delete();
        return response()->json(['message' => 'Pengaduan dihapus'], 200);
    }
}
