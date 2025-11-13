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
        $pengaduan = Pengaduan::with('item')
            ->where('id_user', $user->id)
            ->orderByDesc('tgl_pengajuan')
            ->get();

        // Format response agar Flutter mudah menampilkan:
        // - sertakan nama item sebagai field 'item' (string)
        // - sertakan 'foto' (path dari DB) dan 'foto_url' (jika tersedia dari accessor)
        $response = $pengaduan->map(function ($p) {
            return [
                'id_pengaduan' => $p->id_pengaduan,
                'nama_pengaduan' => $p->nama_pengaduan,
                'deskripsi' => $p->deskripsi,
                'lokasi' => $p->lokasi,
                'status' => $p->status,
                'tgl_pengajuan' => $p->tgl_pengajuan,
                'tgl_selesai' => $p->tgl_selesai,
                'saran_petugas' => $p->saran_petugas,
                // ambil nama item jika relasi ada
                'item' => $p->item ? $p->item->nama_item : null,
                'id_item' => $p->id_item,
                // foto: kirim value dari kolom (path) agar Flutter bisa membangun URL jika perlu
                'foto' => $p->foto,
                // juga sertakan foto_url dari accessor jika ingin langsung menampilkan URL penuh
                'foto_url' => $p->foto_url,
                'foto_penyelesaian' => $p->foto_penyelesaian,
                'foto_penyelesaian_url' => $p->foto_penyelesaian_url,
            ];
        });

        return response()->json($response);
    }

    // POST /api/pengaduan → ajukan pengaduan baru
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_pengaduan' => 'required|string|max:200',
                'deskripsi' => 'required|string',
                'id_lokasi' => 'required|integer|exists:lokasi,id_lokasi',
                'id_item' => 'nullable|integer|exists:items,id_item',
                'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ], [
                'id_lokasi.required' => 'Lokasi harus dipilih',
                'id_lokasi.exists' => 'Lokasi yang dipilih tidak valid',
                'id_item.exists' => 'Item yang dipilih tidak valid',
                'nama_pengaduan.required' => 'Nama pengaduan harus diisi',
                'deskripsi.required' => 'Deskripsi harus diisi',
                'foto.image' => 'File harus berupa gambar',
                'foto.max' => 'Ukuran foto maksimal 2MB',
            ]);

            // Convert id_lokasi ke integer
            $idLokasi = (int) $request->id_lokasi;

            // Ambil nama lokasi dari id_lokasi
            $lokasi = \App\Models\Lokasi::findOrFail($idLokasi);
            $namaLokasi = $lokasi->nama_lokasi;

            $path = null;
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('pengaduan', 'public');
            }

            $pengaduan = Pengaduan::create([
                'nama_pengaduan' => $request->nama_pengaduan,
                'deskripsi' => $request->deskripsi,
                'lokasi' => $namaLokasi, // Simpan nama lokasi sebagai string
                'id_item' => $request->id_item ? (int) $request->id_item : null,
                'foto' => $path,
                'id_user' => $request->user()->id,
                'status' => Pengaduan::STATUS_DIAJUKAN,
                'tgl_pengajuan' => now(),
            ]);

            // muat relasi item lalu kembalikan format yang sama seperti index
            $pengaduan->load('item');

            $result = [
                'id_pengaduan' => $pengaduan->id_pengaduan,
                'nama_pengaduan' => $pengaduan->nama_pengaduan,
                'deskripsi' => $pengaduan->deskripsi,
                'lokasi' => $pengaduan->lokasi,
                'item' => $pengaduan->item ? $pengaduan->item->nama_item : null,
                'id_item' => $pengaduan->id_item,
                'foto' => $pengaduan->foto,
                'foto_url' => $pengaduan->foto_url,
                'status' => $pengaduan->status,
                'tgl_pengajuan' => $pengaduan->tgl_pengajuan,
            ];

            return response()->json($result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/pengaduan/{id} → detail
    public function show(Request $request, $id)
    {
        $pengaduan = Pengaduan::with('item')
            ->where('id_pengaduan', $id)
            ->where('id_user', $request->user()->id)
            ->firstOrFail();

        $result = [
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'nama_pengaduan' => $pengaduan->nama_pengaduan,
            'deskripsi' => $pengaduan->deskripsi,
            'lokasi' => $pengaduan->lokasi,
            'item' => $pengaduan->item ? $pengaduan->item->nama_item : null,
            'id_item' => $pengaduan->id_item,
            'foto' => $pengaduan->foto,
            'foto_url' => $pengaduan->foto_url,
            'foto_penyelesaian' => $pengaduan->foto_penyelesaian,
            'foto_penyelesaian_url' => $pengaduan->foto_penyelesaian_url,
            'status' => $pengaduan->status,
            'tgl_pengajuan' => $pengaduan->tgl_pengajuan,
            'tgl_selesai' => $pengaduan->tgl_selesai,
            'saran_petugas' => $pengaduan->saran_petugas,
        ];

        return response()->json($result);
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
