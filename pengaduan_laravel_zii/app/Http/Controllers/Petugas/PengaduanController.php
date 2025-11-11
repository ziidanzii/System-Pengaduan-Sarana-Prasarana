<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Digunakan untuk mendapatkan ID petugas
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan yang siap diproses oleh petugas.
     */
    public function index()
    {
        // Petugas melihat Disetujui, Diproses, dan Selesai.
        // Urutan: Disetujui (terbaru → lama) → Diproses → Selesai
        $pengaduan = Pengaduan::whereIn('status', ['Disetujui', 'Diproses', 'Selesai'])
                            ->with(['user', 'item']) // Load relasi user dan item
                            ->orderByRaw("
                                CASE
                                    WHEN status = 'Disetujui' THEN 1
                                    WHEN status = 'Diproses' THEN 2
                                    WHEN status = 'Selesai' THEN 3
                                    ELSE 4
                                END,
                                created_at DESC
                            ")
                            ->get();

        return view('petugas.pengaduan.index', compact('pengaduan'));
    }

    /**
     * Menampilkan detail pengaduan.
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['user', 'item', 'petugas'])->findOrFail($id);

        // Cek apakah petugas bisa mengakses pengaduan ini (Disetujui, Diproses, atau Selesai)
        if (!in_array($pengaduan->status, ['Disetujui', 'Diproses', 'Selesai'])) {
            return redirect()->route('petugas.pengaduan.index')
                             ->with('error', 'Pengaduan ini tidak dapat diproses karena status: ' . $pengaduan->status);
        }

        // Asumsi ada method canBeUpdatedByPetugas() di model Pengaduan yang melakukan pengecekan ini,
        // namun pengecekan status di atas sudah cukup untuk konteks ini.

        return view('petugas.pengaduan.show', compact('pengaduan'));
    }

    /**
     * Memperbarui status pengaduan dan mengisi saran petugas.
     */
    public function update(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Pengecekan status untuk memastikan hanya Disetujui atau Diproses yang bisa diupdate
        if (!in_array($pengaduan->status, ['Disetujui', 'Diproses'])) {
            return redirect()->route('petugas.pengaduan.index')
                             ->with('error', 'Pengaduan ini tidak dapat diupdate karena status sudah ' . $pengaduan->status);
        }

        $validated = $request->validate([
            // Petugas hanya bisa memproses atau menyelesaikan
            'status' => 'required|in:Diproses,Selesai',
            'saran_petugas' => 'nullable|string',
            'foto_penyelesaian' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil ID petugas yang sedang login. Asumsi Auth::user()->petugas->id_petugas adalah cara yang benar
        // untuk mendapatkan ID petugas dari relasi.
        $idPetugas = Auth::user()->petugas->id_petugas ?? null;

        if (!$idPetugas) {
             return back()->with('error', 'Akun Anda tidak terhubung dengan data Petugas.');
        }

        if ($request->status === 'Selesai' && !$request->hasFile('foto_penyelesaian') && !$pengaduan->foto_penyelesaian) {
            return back()
                ->withErrors(['foto_penyelesaian' => 'Silakan unggah foto bukti penyelesaian ketika menandai pengaduan sebagai selesai.'])
                ->withInput($request->except('foto_penyelesaian'));
        }

        $updateData = [
            'status' => $request->status,
            'saran_petugas' => $request->saran_petugas,
            'id_petugas' => $idPetugas,
        ];

        if ($request->hasFile('foto_penyelesaian')) {
            if ($pengaduan->foto_penyelesaian && Storage::disk('public')->exists($pengaduan->foto_penyelesaian)) {
                Storage::disk('public')->delete($pengaduan->foto_penyelesaian);
            }

            $updateData['foto_penyelesaian'] = $request->file('foto_penyelesaian')->store('pengaduan/penyelesaian', 'public');
        }

        // Jika status selesai, set tanggal selesai
        if ($request->status == 'Selesai') {
            $updateData['tgl_selesai'] = now();
        }

        // Tidak ada perlakuan khusus lain

        $pengaduan->update($updateData);

        return redirect()->route('petugas.pengaduan.index')
                         ->with('success', 'Status pengaduan berhasil diperbarui menjadi ' . $request->status . '.');
    }

    // Metode destroy tidak diperlukan untuk petugas dalam alur normal
}
