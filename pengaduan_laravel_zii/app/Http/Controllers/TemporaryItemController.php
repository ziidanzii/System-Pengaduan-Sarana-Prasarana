<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\TemporaryItem;
use App\Models\Item;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TemporaryItemController extends Controller
{
    /**
     * Menampilkan halaman daftar item yang belum ada pada data (Temporary Items).
     */
    public function index(Request $request)
    {
        try {
            $query = TemporaryItem::with(['user', 'pengaduan']);

            // Filter Pencarian
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_barang_baru', 'like', $searchTerm)
                      ->orWhere('lokasi_barang_baru', 'like', $searchTerm)
                      ->orWhereHas('user', function ($q_user) use ($searchTerm) {
                          $q_user->where('nama_pengguna', 'like', $searchTerm);
                      });
                });
            }

            // Filter Status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } else {
                // Default: Utamakan status 'pending'
                $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')");
            }

            $temporaryItems = $query->orderBy('created_at', 'desc')->get();

            // Pastikan path view sudah benar
            return view('admin.manajemen_pengaduan.temporary_items', compact('temporaryItems'));

        } catch (\Exception $e) {
            Log::error('Error saat memuat Temporary Items:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memuat data item yang belum ada.');
        }
    }

    /**
     * Menyetujui item sementara: membuat Item baru dan mengaitkannya ke Pengaduan (jika ada).
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $temporaryItem = TemporaryItem::findOrFail($id);

            if ($temporaryItem->status !== 'pending') {
                 DB::rollBack();
                 return redirect()->route('admin.temp.items')
                                 ->with('warning', 'Item ini sudah berstatus: ' . $temporaryItem->status);
            }

            // 1. Buat Item baru di tabel 'items'
            $item = Item::create([
                'nama_item' => $temporaryItem->nama_barang_baru,
                'deskripsi' => 'Item baru dari request pengguna: ' . ($temporaryItem->pengaduan->nama_pengaduan ?? 'N/A'),
            ]);

            // 2. Hubungkan item dengan Lokasi melalui list_lokasi
            $lokasi = Lokasi::where('nama_lokasi', $temporaryItem->lokasi_barang_baru)->first();
            if ($lokasi) {
                // Hubungkan item dengan lokasi melalui tabel pivot list_lokasi
                $item->lokasis()->attach($lokasi->id_lokasi);
            } else {
                // Jika lokasi tidak ditemukan, rollback dan beri error
                DB::rollBack();
                return redirect()->route('admin.temp.items')
                                ->with('error', 'Lokasi "' . $temporaryItem->lokasi_barang_baru . '" tidak ditemukan. Pastikan lokasi sudah terdaftar.');
            }

            // 3. Update status Temporary Item dan hubungkan ke ID Item yang baru
            $temporaryItem->update([
                'status' => 'approved',
                'id_item' => $item->id_item
            ]);

            // 4. Update Pengaduan terkait (Jika Pengaduan awalnya tidak punya ID Item)
            if ($temporaryItem->id_pengaduan) {
                $pengaduan = Pengaduan::find($temporaryItem->id_pengaduan);
                // Hanya update jika pengaduan memang diajukan tanpa id_item (item baru)
                // dan status temporary item ini adalah yang pertama disetujui untuk pengaduan tersebut.
                if ($pengaduan && !$pengaduan->id_item) {
                     $pengaduan->update(['id_item' => $item->id_item]);
                }
            }

            DB::commit();
            return redirect()->route('admin.temp.items')
                             ->with('success', 'Item **' . $temporaryItem->nama_barang_baru . '** berhasil disetujui dan ditambahkan ke inventaris. Pengaduan terkait telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyetujui Temporary Item:', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('admin.temp.items')
                             ->with('error', 'Terjadi kesalahan saat menyetujui item: ' . $e->getMessage());
        }
    }

    /**
     * Menolak item sementara.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500'
        ]);

        try {
            $temporaryItem = TemporaryItem::findOrFail($id);

            if ($temporaryItem->status !== 'pending') {
                 return redirect()->route('admin.temp.items')
                                 ->with('warning', 'Item ini sudah berstatus: ' . $temporaryItem->status);
            }

            $temporaryItem->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan_penolakan
            ]);

            return redirect()->route('admin.temp.items')
                             ->with('success', 'Item **' . $temporaryItem->nama_barang_baru . '** berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error('Error saat menolak Temporary Item:', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('admin.temp.items')
                             ->with('error', 'Terjadi kesalahan saat menolak item: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $temporaryItem = TemporaryItem::findOrFail($id);

            // Periksa status: Hanya izinkan hapus jika status pending atau rejected
            if ($temporaryItem->status === 'approved') {
                 return redirect()->route('admin.temp.items')
                                 ->with('error', 'Item **' . $temporaryItem->nama_barang_baru . '** tidak dapat dihapus karena sudah disetujui dan masuk ke inventaris.');
            }

            $namaItem = $temporaryItem->nama_barang_baru;
            $temporaryItem->delete();

            return redirect()->route('admin.temp.items')
                             ->with('success', 'Item **' . $namaItem . '** berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error saat menghapus Temporary Item:', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('admin.temp.items')
                             ->with('error', 'Terjadi kesalahan saat menghapus item: ' . $e->getMessage());
        }
    }
}
